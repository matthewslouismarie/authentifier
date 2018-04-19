<?php

declare(strict_types=1);

namespace LM\Authentifier\Challenge;

use LM\Authentifier\Model\AuthenticationProcess;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @todo Should be renamed to RequestHandler or something…
 */
interface IChallenge
{
    public function process(
        AuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): ChallengeResponse
    ;
}
