<?php

namespace AsaasPhpSdk\Exceptions;

use Throwable;

/**
 * Represents a rate limiting error (HTTP 429 "Too Many Requests").
 *
 * This exception is thrown when the application has exceeded the allowed number
 * of requests to the Asaas API in a given period. It may contain a recommended
 * waiting time before a new request should be attempted.
 */
class RateLimitException extends AsaasException
{
    /**
     * RateLimitException constructor.
     *
     * @param  string  $message  The Exception message to throw.
     * @param  int  $code  The Exception code, defaulting to 429.
     * @param  ?Throwable  $previous  The previous throwable used for the exception chaining.
     * @param  ?int  $retryAfter  The number of seconds to wait before retrying the request.
     */
    public function __construct(string $message = 'Rate limit exceeded', int $code = 429, ?Throwable $previous = null, public readonly ?int $retryAfter = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the recommended waiting time in seconds before retrying the request.
     *
     * This value is extracted from the 'Retry-After' HTTP header returned by the API.
     *
     * @return ?int The number of seconds to wait, or null if not provided by the API.
     *
     * @example
     * try {
     * // API call
     * } catch (RateLimitException $e) {
     * if ($seconds = $e->getRetryAfter()) {
     * echo "Rate limit hit. Waiting {$seconds} seconds...";
     * sleep($seconds);
     * // Retry logic here
     * }
     * }
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
