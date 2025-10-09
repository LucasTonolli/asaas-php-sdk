<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Exceptions;

use Exception;
use Throwable;

/**
 * Base exception for all errors originating from the Asaas PHP SDK.
 *
 * This exception serves as a common parent for all custom exceptions thrown by
 * this library. It allows developers to use a single `catch` block to handle
 * any error from the SDK, making error handling more predictable.
 *
 * @example
 * try {
 * $customer = $asaas->customer->create([...]);
 * } catch (AsaasException $e) {
 * // Catches any specific SDK error, like ValidationException, NotFoundException, etc.
 * error_log('An error occurred with the Asaas SDK: ' . $e->getMessage());
 * }
 */

class AsaasException extends Exception
{
    /**
     * AsaasException constructor.
     *
     * @param  string  $message The Exception message to throw.
     * @param  int  $code The Exception code.
     * @param  ?Throwable  $previous The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
