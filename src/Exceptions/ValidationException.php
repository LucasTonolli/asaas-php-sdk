<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

/**
 * Represents a data validation error (typically HTTP 400).
 *
 * This exception is thrown in two main scenarios:
 * 1. When the Asaas API returns an HTTP 400 Bad Request status, indicating that
 * the data sent was invalid. In this case, it often contains a detailed
 * list of errors from the API.
 * 2. As a wrapper for internal validation errors (like `InvalidCustomerDataException`)
 * to provide a consistent, public-facing validation exception to the SDK user.
 */
class ValidationException extends AsaasException
{
    /**
     * ValidationException constructor.
     *
     * @param  string  $message  The Exception message to throw.
     * @param  int  $code  The Exception code.
     * @param  ?Throwable  $previous  The previous throwable used for the exception chaining.
     * @param  array<int, array<string, string>>  $errors  A detailed list of validation errors, often from the API.
     */
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, public readonly array $errors = [])
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the detailed list of validation errors.
     *
     * This is useful for retrieving field-specific error messages from the API
     * to display to an end-user.
     *
     * @return array<int, array<string, string>> An array of error details. Each error is typically
     *                                           an associative array with keys like 'code' and 'description'.
     *
     * @example
     * try {
     * $asaas->customer->create(['email' => 'invalid']);
     * } catch (ValidationException $e) {
     * foreach ($e->getErrors() as $error) {
     * echo "Error: " . $error['description'] . "\n";
     * //> Error: O campo E-mail não está em um formato válido.
     * }
     * }
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
