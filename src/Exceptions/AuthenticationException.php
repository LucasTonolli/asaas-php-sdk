<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

class AuthenticationException extends AsaasException
{
    public function __construct(string $message = 'Authentication failed. Check your API token.', int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
