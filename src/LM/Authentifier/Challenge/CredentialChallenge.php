<?php

namespace LM\Authentifier\Challenge;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\RequestDatum;
use LM\Common\DataStructure\TypedMap;
use LM\Common\Model\StringObject;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Twig_Environment;

class CredentialChallenge implements IChallenge
{
    private $appConfig;

    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    public function __construct(
        IApplicationConfiguration $appConfig,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig)
    {
        $this->appConfig = $appConfig;
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
    }

    /**
     * @todo Store the registrations in the datamanager differently.
     * @todo Support for multiple key authentications.
     * @todo Remove break statements.
     */
    public function process(
        AuthenticationProcess $process,
        ?RequestInterface $httpRequest): ChallengeResponse
    {
        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }
        if ($form->isSubmitted()) {
            if (!$this->appConfig->isExistingMember($form['username']->getData())) {
                $form->addError(new FormError('Invalid credentials'));
            } else {
                $member = $this->appConfig->getMember($form['username']->getData());
                if (!password_verify($form['password']->getData(), $member->getHashedPassword())) {
                    $form->addError(new FormError('Invalid credentials'));                
                }
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $authProcess = new AuthenticationProcess($process
                ->getDataManager()
                ->add(
                    'username',
                    new StringObject($form['username']->getData()),
                    StringObject::class))
            ;

            return new ChallengeResponse(
                $authProcess,
                null,
                true,
                true)
            ;
        }
        $httpResponse = new Response($this->twig->render("credentials.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process,
            $httpResponse,
            $form->isSubmitted(),
            false)
        ;
    }
}
