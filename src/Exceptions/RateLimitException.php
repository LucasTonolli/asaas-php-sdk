<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

class RateLimitException extends AsaasException
{
    public function __construct(string $message = 'Rate limit exceeded', int $code = 429, ?Throwable $previous = null, public readonly ?int $retryAfter = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
