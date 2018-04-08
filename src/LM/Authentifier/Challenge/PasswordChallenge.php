<?php

namespace LM\Authentifier\Challenge;

use Firehed\U2F\Registration;
use Psr\Http\Message\RequestInterface;
use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\DataManager;
use LM\Authentifier\Model\RequestDatum;
use LM\Authentifier\U2f\U2fAuthenticationManager;
use LM\Common\Model\StringObject;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class PasswordChallenge implements IChallenge
{
    private $appConfig;

    private $formFactory;

    private $httpFoundationFactory;

    private $twig;

    private $u2fAuthenticationManager;

    public function __construct(
        IApplicationConfiguration $appConfig,
        FormFactoryInterface $formFactory,
        HttpFoundationFactory $httpFoundationFactory,
        Twig_Environment $twig,
        U2fAuthenticationManager $u2fAuthenticationManager)
    {
        $this->appConfig = $appConfig;
        $this->formFactory = $formFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->twig = $twig;
        $this->u2fAuthenticationManager = $u2fAuthenticationManager;
    }

    /**
     * @todo Store the registrations in the datamanager differently.
     * @todo Support for multiple key authentications.
     */
    public function process(
        AuthenticationProcess $process,
        ?RequestInterface $httpRequest): ChallengeResponse
    {
        $username = $process
            ->getDataManager()
            ->get(RequestDatum::KEY_PROPERTY, "username")
            ->getOnlyValue()
            ->getObject(RequestDatum::VALUE_PROPERTY, StringObject::class)
            ->toString()
        ;

        $member = $this
            ->appConfig
            ->getMember($username)
        ;

        $form = $this
            ->formFactory
            ->createBuilder()
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        if (null !== $httpRequest) {
            $form->handleRequest($this->httpFoundationFactory->createRequest($httpRequest));
        }

        if ($form->isSubmitted() && !password_verify($form['password']->getData(), $member->getHashedPassword())) {
            $form
                ->get('password')
                ->addError(new FormError('The password is invalid.'))
            ;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            return new ChallengeResponse(
                $process,
                null,
                false,
                true)
            ;
        }

        $response = new Response($this->twig->render("password.html.twig", [
            "form" => $form->createView(),
        ]));

        return new ChallengeResponse(
            $process, 
            $response,
            $form->isSubmitted(),
            false)
        ;
    }
}
