<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions;

use Exception;
use Throwable;

class AsaasException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
