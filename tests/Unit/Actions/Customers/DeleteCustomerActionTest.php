<?php

use AsaasPhpSdk\Actions\Customers\DeleteCustomerAction;
use AsaasPhpSdk\Exceptions\AuthenticationException;
use AsaasPhpSdk\Exceptions\NotFoundException;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;

describe('Delete Customer Action', function (): void {

    it('deletes a customer successfully (200)', function (): void {
        $client = mockClient([
            mockResponse([
                'deleted' => true,
                'id' => 'cus_123',
            ], 200),
        ]);

        $action = new DeleteCustomerAction($client, new ResponseHandler);

        $result = $action->handle('cus_123');

        expect($result)->toBeArray()
            ->and($result['deleted'])->toBeTrue()
            ->and($result['id'])->toBe('cus_123');
    });

    it('throws ValidationException on 400 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Invalid request', 400, [
                ['description' => 'Customer cannot be deleted'],
            ]),
        ]);

        $action = new DeleteCustomerAction($client, new ResponseHandler);

        $action->handle('cus_invalid');
    })->throws(ValidationException::class, 'Customer cannot be deleted');

    it('throws AuthenticationException on 401 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Unauthorized', 401),
        ]);

        $action = new DeleteCustomerAction($client, new ResponseHandler);

        $action->handle('cus_unauthorized');
    })->throws(AuthenticationException::class, 'Invalid API token or unauthorized access');

    it('throws NotFoundException on 404 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Resource not found', 404),
        ]);

        $action = new DeleteCustomerAction($client, new ResponseHandler);

        $action->handle('non-existent-id');
    })->throws(NotFoundException::class, 'Resource not found');

    it('throws InvalidArgumentException when ID is empty', function (): void {
        $client = mockClient();

        $action = new DeleteCustomerAction($client, new ResponseHandler);

        $action->handle('');
    })->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
