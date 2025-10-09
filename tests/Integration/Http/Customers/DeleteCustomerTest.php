<?php

use AsaasPhpSdk\Exceptions\NotFoundException;

describe('Delete Customer', function () {
    beforeEach(function () {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('deletes a customer successfully', function () {
        $createCustomerResponse = $this->asaasClient->customer()->create([
            'name' => 'John Doe'.uniqid(),
            'cpfCnpj' => '898.879.660-88',
        ]);

        $customerId = $createCustomerResponse['id'];

        $response = $this->asaasClient->customer()->delete($customerId);
        expect($response['id'])->toBe($customerId)
            ->and($response['deleted'])->toBe(true);
    });

    it('throws an exception when the customer is not found (404)', function () {
        $this->asaasClient->customer()->delete('cus_notfound');
    })->throws(NotFoundException::class, 'Resource not found');

    it('throws an exception when the customer ID is empty', function () {
        $this->asaasClient->customer()->delete('');
    })->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
