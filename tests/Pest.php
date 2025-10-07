<?php

declare(strict_types=1);

use AsaasPhpSdk\AsaasClient;
use AsaasPhpSdk\Config\AsaasConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses()->in('Unit');
uses()->in('Integration');
uses()->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeValidCpf', function () {
    $cpf = preg_replace('/\D/', '', $this->value);
    expect(strlen($cpf))->toBe(11);

    return $this;
});

expect()->extend('toBeValidCnpj', function () {
    $cnpj = preg_replace('/\D/', '', $this->value);
    expect(strlen($cnpj))->toBe(14);

    return $this;
});

expect()->extend('toBeValidEmail', function () {
    expect(filter_var($this->value, FILTER_VALIDATE_EMAIL))->not->toBeFalse();

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Create a mock HTTP client with predefined responses
 */
function mockClient(array $responses = []): Client
{
    $mock = new MockHandler($responses);
    $handlerStack = HandlerStack::create($mock);

    return new Client(['handler' => $handlerStack, 'http_errors' => false]);
}

/**
 * Create a successful mock response
 */
function mockResponse(array $body, int $status = 200): Response
{
    return new Response(
        status: $status,
        headers: ['Content-Type' => 'application/json'],
        body: json_encode($body)
    );
}

/**
 * Create an error mock response
 */
function mockErrorResponse(string $message, int $status = 400, array $errors = []): Response
{
    $body = [
        'message' => $message,
    ];

    if (! empty($errors)) {
        $body['errors'] = $errors;
    }

    return new Response(
        status: $status,
        headers: ['Content-Type' => 'application/json'],
        body: json_encode($body)
    );
}

/**
 * Create a test config
 */
function testConfig(): AsaasConfig
{
    return new AsaasConfig(
        token: 'test_token_123',
        isSandbox: true
    );
}

/**
 * Create a test Asaas client
 */
function testClient(?Client $httpClient = null): AsaasClient
{
    $config = testConfig();

    if ($httpClient) {
        // Inject mock HTTP client for testing
        $client = new AsaasClient($config);

        // Note: You'll need to add a method to inject the client or use reflection
        return $client;
    }

    return new AsaasClient($config);
}

function sandboxToken(): string
{
    return $_ENV['ASAAS_SANDBOX_TOKEN'];
}
function sandboxConfig(): AsaasPhpSdk\Config\AsaasConfig
{
    return new AsaasPhpSdk\Config\AsaasConfig(
        token: sandboxToken(),
        isSandbox: true
    );
}
