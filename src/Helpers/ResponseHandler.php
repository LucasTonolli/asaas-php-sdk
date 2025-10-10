<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Helpers;

use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Exceptions\AuthenticationException;
use AsaasPhpSdk\Exceptions\NotFoundException;
use AsaasPhpSdk\Exceptions\RateLimitException;
use AsaasPhpSdk\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

/**
 * Handles and standardizes API responses.
 *
 * This class is a critical component of the SDK's error handling strategy.
 * Its primary responsibility is to take a raw PSR-7 response and either parse a
 * successful response body or translate an HTTP error status code into a
 * specific, typed `AsaasException`.
 *
 * @internal This is an internal helper class and is not intended for public use by SDK consumers.
 */
final class ResponseHandler
{
    /**
     * Main entry point to process an HTTP response.
     *
     * This method validates the response's HTTP status code and, if successful,
     * parses and returns the JSON body. If the status indicates an error, it will
     * throw a specific exception.
     *
     * @param  ResponseInterface  $response  The PSR-7 response from the HTTP client.
     * @return array<string, mixed> The decoded JSON body as an associative array.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    public function handle(ResponseInterface $response): array
    {
        $this->validateResponse($response);

        return $this->parseBody($response);
    }

    /**
     * Validates the HTTP status code of the response.
     *
     * If the status is successful (2xx), the method does nothing. If it's an
     * error code (4xx or 5xx), it throws a corresponding SDK exception.
     *
     * @param  ResponseInterface  $response  The response to validate.
     *
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws ValidationException
     * @throws RateLimitException
     * @throws ApiException
     */
    private function validateResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        try {
            $body = $this->parseBody($response);
        } catch (ApiException) {
            $body = [];
        }
        $errorMessage = $this->extractErrorMessage($body);

        match (true) {
            $statusCode === 401 => throw new AuthenticationException(
                $errorMessage ?? 'Invalid API token or unauthorized access'
            ),
            $statusCode === 400 => throw new ValidationException(
                $errorMessage ?? 'Invalid data provided',
                0,
                null,
                $body['errors'] ?? []
            ),
            $statusCode === 404 => throw new NotFoundException(
                $errorMessage ?? 'Resource not found'
            ),
            $statusCode === 429 => throw new RateLimitException(
                $errorMessage ?? 'Rate limit exceeded. Please try again later.',
                429,
                null,
                $this->extractRetryAfter($response)
            ),
            $statusCode >= 500 => throw new ApiException(
                $errorMessage ?? 'Asaas API server error. Please try again later.',
                $statusCode
            ),
            default => throw new ApiException(
                $errorMessage ?? 'Unexpected error occurred',
                $statusCode
            ),
        };
    }

    /**
     * Parses the JSON response body into an associative array.
     *
     * @param  ResponseInterface  $response  The response containing the body.
     * @return array<string, mixed> The decoded data.
     *
     * @throws ApiException If the response body contains invalid JSON.
     */
    private function parseBody(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();

        if (empty($body)) {
            return [];
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Invalid JSON response from API: ' . json_last_error_msg()
            );
        }

        return $data ?? [];
    }

    /**
     * Extracts a concatenated error message from the response body.
     *
     * @param  array<string, mixed>  $body  The decoded response body.
     * @return ?string A single string with all error messages, or null if none are found.
     */
    private function extractErrorMessage(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        if (isset($body['errors']) && is_array($body['errors'])) {
            $errors = array_map(
                fn($error) => is_array($error)
                    ? ($error['description'] ?? $error['message'] ?? 'Unknown error')
                    : (string) $error,
                $body['errors']
            );

            return implode('; ', $errors);
        }

        return null;
    }

    /**
     * Extracts the 'Retry-After' header value from the response.
     *
     * @param  ResponseInterface  $response  The PSR-7 response.
     * @return ?int The number of seconds to wait, or null if the header is not present.
     */
    private function extractRetryAfter(ResponseInterface $response): ?int
    {
        $retryAfter = $response->getHeader('Retry-After');

        if (empty($retryAfter)) {
            return null;
        }

        return (int) $retryAfter[0];
    }
}
