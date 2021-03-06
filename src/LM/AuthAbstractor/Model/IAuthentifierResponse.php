<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface for representing the value returned when the kernel finishes
 * processing the HTTP request.
 */
interface IAuthentifierResponse
{
    /**
     * @api
     * @return IAuthenticationProcess The authentication process.
     */
    public function getAuthenticationProcess(): IAuthenticationProcess;

    /**
     * @api
     * @return null|ResponseInterface The HTTP response.
     */
    public function getHttpResponse(): ?ResponseInterface;
}
