<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

class ApiException extends AsaasException
{
    public function __construct(string $message = 'An API error occurred', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
