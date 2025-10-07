<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Helpers;

use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Exceptions\AuthenticationException;
use AsaasPhpSdk\Exceptions\NotFoundException;
use AsaasPhpSdk\Exceptions\RateLimitException;
use AsaasPhpSdk\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

final class ResponseHandler
{
    public function handle(ResponseInterface $response): array
    {
        $this->validateResponse($response);

        return $this->parseBody($response);
    }

    private function validateResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        $body = $this->parseBody($response);
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
     * Parse response body as JSON
     *
     * @throws ApiException If JSON is invalid
     */
    private function parseBody(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Invalid JSON response from API: '.json_last_error_msg()
            );
        }

        return $data ?? [];
    }

    /**
     * Extract error message from API response body
     */
    private function extractErrorMessage(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        if (isset($body['errors']) && is_array($body['errors'])) {
            $errors = array_map(
                fn ($error) => is_array($error)
                    ? ($error['description'] ?? $error['message'] ?? 'Unknown error')
                    : (string) $error,
                $body['errors']
            );

            return implode('; ', $errors);
        }

        return null;
    }

    /**
     * Extract Retry-After header for rate limiting
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
