<?php

declare(strict_types=1);

use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Exceptions\AuthenticationException;
use AsaasPhpSdk\Exceptions\NotFoundException;
use AsaasPhpSdk\Exceptions\RateLimitException;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helper\ResponseHandler;

describe('ResponseHandler', function () {

    beforeEach(function () {
        $this->handler = new ResponseHandler;
    });

    it('handles successful 200 response', function () {
        $response = mockResponse([
            'id' => 'cus_123',
            'name' => 'João Silva',
        ], 200);

        $result = $this->handler->handle($response);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('cus_123')
            ->and($result['name'])->toBe('João Silva');
    });

    it('handles successful 201 response', function () {
        $response = mockResponse([
            'id' => 'cus_123',
            'created' => true,
        ], 201);

        $result = $this->handler->handle($response);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('cus_123');
    });

    it('handles empty successful response', function () {
        $response = mockResponse([], 204);

        $result = $this->handler->handle($response);

        expect($result)->toBeArray()
            ->and($result)->toBeEmpty();
    });

    it('throws AuthenticationException on 401', function () {
        $response = mockErrorResponse('Invalid API token', 401);

        $this->handler->handle($response);
    })->throws(AuthenticationException::class, 'Invalid API token');

    it('throws ValidationException on 400', function () {
        $response = mockErrorResponse('Invalid data', 400, [
            ['description' => 'Name is required'],
            ['description' => 'Email is invalid'],
        ]);

        $this->handler->handle($response);
    })->throws(ValidationException::class);

    it('throws NotFoundException on 404', function () {
        $response = mockErrorResponse('Customer not found', 404, [
            ['description' => 'Customer not found'],
        ]);

        $this->handler->handle($response);
    })->throws(NotFoundException::class, 'Customer not found');

    it('throws RateLimitException on 429', function () {
        $response = mockErrorResponse('Rate limit exceeded', 429);

        $this->handler->handle($response);
    })->throws(RateLimitException::class, 'Rate limit exceeded');

    it('throws ApiException on 500', function () {
        $response = mockErrorResponse('Internal server error', 500, [
            ['description' => 'Internal server error'],
        ]);

        $this->handler->handle($response);
    })->throws(ApiException::class, 'Internal server error');

    it('throws ApiException on 503', function () {
        $response = mockErrorResponse('Service unavailable', 503, [
            ['description' => 'Service unavailable'],
        ]);

        $this->handler->handle($response);
    })->throws(ApiException::class, 'Service unavailable');

    it('extracts error message from message field', function () {
        $response = mockErrorResponse('Custom error message', 400, [
            ['description' => 'Custom error message'],
        ]);

        try {
            $this->handler->handle($response);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toBe('Custom error message');
        }
    });

    it('extracts multiple error messages from errors array', function () {
        $response = mockErrorResponse('Validation failed', 400, [
            ['description' => 'Name is required'],
            ['description' => 'Email is invalid'],
        ]);

        try {
            $this->handler->handle($response);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toContain('Name is required')
                ->and($e->getMessage())->toContain('Email is invalid');
        }
    });

    it('throws ApiException for invalid JSON response', function () {
        $response = new GuzzleHttp\Psr7\Response(
            200,
            ['Content-Type' => 'application/json'],
            'invalid json{'
        );

        $this->handler->handle($response);
    })->throws(ApiException::class, 'Invalid JSON');

    it('handles response with no error message', function () {
        $response = new GuzzleHttp\Psr7\Response(
            400,
            ['Content-Type' => 'application/json'],
            json_encode([])
        );

        try {
            $this->handler->handle($response);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toContain('Invalid data provided');
        }
    });
});
