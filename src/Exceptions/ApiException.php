<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

/**
 * Represents a generic error related to the Asaas API.
 *
 * This exception is thrown in the following scenarios:
 * - When the API returns a server-side error (HTTP 5xx).
 * - When an unhandled client-side HTTP error occurs (e.g., connection issues).
 * - When the API returns an unexpected or unmapped error status code.
 *
 * It serves as a general catch-all for API communication failures that are not
 * more specific, like AuthenticationException or NotFoundException.
 */

class ApiException extends AsaasException
{
    /**
     * ApiException constructor.
     *
     * @param  string  $message The Exception message to throw.
     * @param  int  $code The Exception code, often corresponding to the HTTP status code.
     * @param  ?Throwable  $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = 'An API error occurred', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
