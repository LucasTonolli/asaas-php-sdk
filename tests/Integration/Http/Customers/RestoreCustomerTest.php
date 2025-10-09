<?php

use AsaasPhpSdk\Exceptions\NotFoundException;

describe('Restore Customer', function () {
	beforeEach(function () {
		$config = sandboxConfig();
		$this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
	});

	it('restores a customer successfully', function () {
		$createCustomerResponse = $this->asaasClient->customer()->create([
			'name' => 'John Doe' . uniqid(),
			'cpfCnpj' => '898.879.660-88',
		]);

		$customerId = $createCustomerResponse['id'];

		$this->asaasClient->customer()->delete($customerId);

		$response = $this->asaasClient->customer()->restore($customerId);
		expect($response['id'])->toBe($customerId)
			->and($response['deleted'])->toBe(false)
			->and($response)->toHaveKeys(CUSTOMER_KEYS);
	});

	it('throws an exception when the customer is not found (404)', function () {
		$this->asaasClient->customer()->restore('cus_notfound');
	})->throws(NotFoundException::class, 'Resource not found');

	it('throws an exception when the customer ID is empty', function () {
		$this->asaasClient->customer()->restore('');
	})->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
