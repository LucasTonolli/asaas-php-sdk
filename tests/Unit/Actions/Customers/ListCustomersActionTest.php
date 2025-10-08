<?php

use AsaasPhpSdk\Actions\Customers\ListCustomersAction;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;
use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;

describe('List Customers Action', function () {

	it('lists customers successfully', function () {
		$client = mockClient([
			mockResponse([
				'object' => 'list',
				'totalCount' => 2,
				'limit' => 10,
				'offset' => 0,
				'hasMore' => false,
				'data' => [
					[
						'id' => 'cus_001',
						'name' => 'Maria Oliveira',
						'cpfCnpj' => '12345678900',
						'email' => 'maria@example.com',
					],
					[
						'id' => 'cus_002',
						'name' => 'João Souza',
						'cpfCnpj' => '98765432100',
						'email' => 'joao@example.com',
					],
				],
			], 200),
		]);

		$action = new ListCustomersAction($client, new ResponseHandler);

		$dto = ListCustomersDTO::fromArray([
			'limit' => 2,
			'offset' => 0,
			'name' => 'Maria',
		]);

		$result = $action->handle($dto);

		expect($result)->toBeArray()
			->and($result['object'])->toBe('list')
			->and($result['totalCount'])->toBe(2)
			->and($result['limit'])->toBe(10)
			->and($result['offset'])->toBe(0)
			->and($result['hasMore'])->toBeFalse()
			->and($result['data'])->toBeArray()
			->and($result['data'][0]['name'])->toBe('Maria Oliveira')
			->and($result['data'][1]['id'])->toBe('cus_002');
	});

	it('throws ValidationException on 400 error', function () {
		$client = mockClient([
			mockErrorResponse('Invalid parameters', 400, [
				['description' => 'Limit must be less than or equal to 100'],
			]),
		]);

		$action = new ListCustomersAction($client, new ResponseHandler);

		$dto = ListCustomersDTO::fromArray([
			'limit' => 1000, // inválido
		]);

		$action->handle($dto);
	})->throws(ValidationException::class, 'Limit must be less than or equal to 100');

	it('throws ApiException on network connection error', function () {
		$client = mockClient([
			new ConnectException(
				'Connection failed',
				new Request('GET', 'customers')
			),
		]);

		$action = new ListCustomersAction($client, new ResponseHandler);

		$dto = ListCustomersDTO::fromArray([
			'limit' => 10,
		]);

		$action->handle($dto);
	})->throws(ApiException::class, 'Failed to connect to Asaas API: Connection failed');
});
