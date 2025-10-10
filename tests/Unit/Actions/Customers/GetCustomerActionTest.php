<?php

use AsaasPhpSdk\Actions\Customers\GetCustomerAction;
use AsaasPhpSdk\Exceptions\AuthenticationException;
use AsaasPhpSdk\Exceptions\NotFoundException;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('GetCustomerAction', function (): void {

    it('retrieves a customer successfully (200)', function (): void {
        $customerId = 'cus_123';

        $client = mockClient([
            mockResponse([
                'id' => $customerId,
                'name' => 'Maria Oliveira',
                'email' => 'maria@example.com',
                'cpfCnpj' => '12345678900',
                'object' => 'customer',
            ], 200),
        ]);

        $action = new GetCustomerAction($client, new ResponseHandler);

        $result = $action->handle($customerId);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe($customerId)
            ->and($result['name'])->toBe('Maria Oliveira')
            ->and($result['object'])->toBe('customer');
    });

    it('throws ValidationException on 400 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Invalid customer ID', 400, [
                ['description' => 'ID format is invalid'],
            ]),
        ]);

        $action = new GetCustomerAction($client, new ResponseHandler);

        $action->handle('invalid-id');
    })->throws(ValidationException::class, 'ID format is invalid');

    it('throws AuthenticationException on 401 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Unauthorized', 401),
        ]);

        $action = new GetCustomerAction($client, new ResponseHandler);

        $action->handle('cus_unauth');
    })->throws(AuthenticationException::class, 'Invalid API token or unauthorized access');

    it('throws NotFoundException on 404 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Customer not found', 404),
        ]);

        $action = new GetCustomerAction($client, new ResponseHandler);

        $action->handle('cus_notfound');
    })->throws(NotFoundException::class, 'Resource not found');

    it('throws InvalidArgumentException when ID is empty', function (): void {
        $client = mockClient([]);
        $action = new GetCustomerAction($client, new ResponseHandler);

        $action->handle('');
    })->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
