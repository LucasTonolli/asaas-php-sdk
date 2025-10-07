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

final class HttpClientFactory
{
    private const MAX_RETRIES = 3;

    private const RETRY_DELAY_MS = 1000;

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

    private static function createLoggingMiddleware(): callable
    {
        return Middleware::mapRequest(function (RequestInterface $request): RequestInterface {
            error_log(sprintf(
                '[Asaas] %s %s {%s}',
                $request->getMethod(),
                $request->getUri(),
                $request->getBody()->getContents()
            ));

            return $request;
        });
    }
}
