<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

class ValidationException extends AsaasException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, public readonly array $errors = [])
    {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
