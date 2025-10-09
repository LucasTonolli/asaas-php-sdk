<?php

use AsaasPhpSdk\Actions\Customers\RestoreCustomerAction;
use AsaasPhpSdk\Exceptions\AuthenticationException;
use AsaasPhpSdk\Exceptions\NotFoundException;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;


describe('Restore Customer Action', function () {

	it('Restore a customer successfully (200)', function () {
		$client = mockClient([
			mockResponse([
				'object' => 'customer',
				'id' => 'cus_123',
				'dateCreated' => '2023-06-01T00:00:00.000Z',
				'name' => 'John Doe',
				'deleted' => false
			], 200),
		]);

		$action = new RestoreCustomerAction($client, new ResponseHandler);

		$result = $action->handle('cus_123');

		expect($result)->toBeArray()
			->and($result['deleted'])->toBeFalse()
			->and($result['id'])->toBe('cus_123');
	});

	it('throws ValidationException on 400 error', function () {
		$client = mockClient([
			mockErrorResponse('Invalid request', 400, [
				['description' => 'Customer cannot be restored'],
			]),
		]);

		$action = new RestoreCustomerAction($client, new ResponseHandler);

		$action->handle('cus_invalid');
	})->throws(ValidationException::class, 'Customer cannot be restored');

	it('throws AuthenticationException on 401 error', function () {
		$client = mockClient([
			mockErrorResponse('Unauthorized', 401),
		]);

		$action = new RestoreCustomerAction($client, new ResponseHandler);

		$action->handle('cus_unauthorized');
	})->throws(AuthenticationException::class, 'Invalid API token or unauthorized access');


	it('throws NotFoundException on 404 error', function () {
		$client = mockClient([
			mockErrorResponse('Resource not found', 404),
		]);

		$action = new RestoreCustomerAction($client, new ResponseHandler);

		$action->handle('non-existent-id');
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws InvalidArgumentException when ID is empty', function () {
		$client = mockClient();

		$action = new RestoreCustomerAction($client, new ResponseHandler);

		$action->handle('');
	})->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
