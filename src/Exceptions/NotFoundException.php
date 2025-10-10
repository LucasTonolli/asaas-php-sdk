<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

/**
 * Represents a "Resource Not Found" error (HTTP 404).
 *
 * This exception is thrown when an operation is attempted on a resource that
 * does not exist, such as trying to get, update, or delete an entity with an
 * identifier that is not found in the Asaas system.
 */

class NotFoundException extends AsaasException
{
    /**
     * NotFoundException constructor.
     *
     * @param  string  $message The Exception message to throw.
     * @param  int  $code The Exception code, defaulting to 404.
     * @param  ?Throwable  $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = 'Resource not found', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
