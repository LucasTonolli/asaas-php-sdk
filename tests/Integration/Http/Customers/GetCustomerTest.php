<?php


describe('Get Customer', function () {

	beforeEach(function () {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
	});

	it('retrieves a customer successfully (200)', function () {
		$customerId = null;

		$getCustomersResponse = $this->asaasClient->customer()->list([
			'limit' => 1,
			'cpfCnpj' => '00264272000107',
		]);


		if (empty($getCustomersResponse['data'])) {
			$createCustomerResponse = $this->asaasClient->customer()->create([
				'name' => 'Maria Oliveira',
				'cpfCnpj' => '00264272000107',
			]);
			$customerId = $createCustomerResponse['id'];
		} else {
			$customerId = $getCustomersResponse['data'][0]['id'];
		}

		$response = $this->asaasClient->customer()->get($customerId);
		expect($response)->toBeArray()
			->and($response['id'])->toBe($customerId)
			->and($response['name'])->toBe('Maria Oliveira')
			->and($response['cpfCnpj'])->toBe('00264272000107')
			->and($response)->toHaveKeys(CUSTOMER_KEYS);
	});

	it('throws an exception when the customer is not found (404)', function () {
		expect(fn() => $this->asaasClient->customer()->get('invalid-customer-id'))->toThrow(AsaasPhpSdk\Exceptions\NotFoundException::class, 'Resource not found');
	});

	it('throws an exception when the customer ID is empty', function () {
		expect(fn() => $this->asaasClient->customer()->get(''))->toThrow(\InvalidArgumentException::class, 'Customer ID cannot be empty');
	});

	it('matches the expected response structure', function () {
		$getCustomersResponse = $this->asaasClient->customer()->list([
			'limit' => 1,
			'cpfCnpj' => '00264272000107',
		]);

		if (empty($getCustomersResponse['data'])) {
			$createCustomerResponse = $this->asaasClient->customer()->create([
				'name' => 'Maria Oliveira',
				'cpfCnpj' => '00264272000107',
			]);
			$customerId = $createCustomerResponse['id'];
		} else {
			$customerId = $getCustomersResponse['data'][0]['id'];
		}

		$response = $this->asaasClient->customer()->get($customerId);
		expect($response['id'])->toBe($customerId);
		expect($response)->toHaveKeys(CUSTOMER_KEYS);
	});
});
