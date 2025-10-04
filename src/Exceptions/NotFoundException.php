<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

class NotFoundException extends AsaasException
{
    public function __construct(string $message = 'Resource not found', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
