<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Helpers;

use AsaasPhpSdk\Config\AsaasConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A factory for creating a pre-configured Guzzle HTTP client.
 *
 * This class centralizes all HTTP client configuration for the SDK. It sets
 * default headers, timeouts, and attaches crucial middleware for resilient
 * API communication, such as automatic retries and request logging.
 *
 * @internal This is an internal helper class and is not intended for public use by SDK consumers.
 */
final class HttpClientFactory
{
    /** @var int The maximum number of times to retry a failed request. */
    private const MAX_RETRIES = 3;

    /** @var int The base delay in milliseconds between retries. */
    private const RETRY_DELAY_MS = 1000;

    /**
     * Creates and configures a new Guzzle Client instance based on the provided settings.
     *
     * @param  AsaasConfig  $config  The configuration object with API token and environment settings.
     * @return Client A fully configured GuzzleHttp\Client instance.
     */
    public static function make(AsaasConfig $config): Client
    {
        $stack = HandlerStack::create();

        $stack->push(self::createRetryMiddleware());

        if ($config->isSandbox() && $config->isLogsEnabled()) {
            $stack->push(self::createLoggingMiddleware());
        }

        return new Client([
            'base_uri' => $config->getBaseUrl(),
            'timeout' => 30,
            'connect_timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'access_token' => $config->getToken(),
                'User-Agent' => 'AsaasPhpSdk/1.0 PHP/'.phpversion(),
            ],
            'handler' => $stack,
            'http_errors' => false,
        ]);
    }

    /**
     * Creates the retry middleware for the Guzzle client.
     *
     * This middleware will retry requests up to MAX_RETRIES times if a connection
     * error occurs or if the API returns a retryable status code (429, 500, 502, 503, 504).
     * The delay between retries increases linearly.
     *
     * @return callable The Guzzle retry middleware.
     *
     * @internal
     */
    private static function createRetryMiddleware(): callable
    {
        return Middleware::retry(
            function (
                int $retries,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?RequestException $exception = null
            ): bool {
                if ($retries >= self::MAX_RETRIES) {
                    return false;
                }

                if ($exception instanceof ConnectException) {
                    return true;
                }

                if ($response && in_array($response->getStatusCode(), [429, 500, 502, 503, 504])) {
                    return true;
                }

                return false;
            },
            function (int $retries): int {
                return $retries * self::RETRY_DELAY_MS;
            }
        );
    }

    /**
     * Creates the request logging middleware for the Guzzle client.
     *
     * This middleware logs the request method, URI, and body to the PHP error log.
     * It is only intended for use in the sandbox environment for debugging purposes.
     *
     * @return callable The Guzzle logging middleware.
     *
     * @internal
     */
    private static function createLoggingMiddleware(): callable
    {
        return Middleware::mapRequest(function (RequestInterface $request): RequestInterface {
            $stream = $request->getBody();
            $body = (string) $stream;
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            error_log(sprintf(
                '[Asaas] %s %s {%s}',
                $request->getMethod(),
                $request->getUri(),
                $body
            ));

            return $request;
        });
    }
}
