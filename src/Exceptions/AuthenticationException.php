<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

/**
 * Represents an authentication error (HTTP 401).
 *
 * This exception is thrown when the Asaas API returns a 401 Unauthorized
 * status, which typically occurs when the provided API token is missing,
 * invalid, or does not have the required permissions for the operation.
 */
class AuthenticationException extends AsaasException
{
    /**
     * AuthenticationException constructor.
     *
     * @param  string  $message  The Exception message to throw.
     * @param  int  $code  The Exception code, defaulting to 401.
     * @param  ?Throwable  $previous  The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = 'Authentication failed. Check your API token.', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
