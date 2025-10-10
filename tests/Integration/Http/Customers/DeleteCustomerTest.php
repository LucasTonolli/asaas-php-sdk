<?php

use AsaasPhpSdk\Exceptions\NotFoundException;

describe('Delete Customer', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('deletes a customer successfully', function (): void {
        $createCustomerResponse = $this->asaasClient->customer()->create([
            'name' => 'John Doe'.uniqid(),
            'cpfCnpj' => '898.879.660-88',
        ]);

        $customerId = $createCustomerResponse['id'];

        $response = $this->asaasClient->customer()->delete($customerId);
        expect($response['id'])->toBe($customerId)
            ->and($response['deleted'])->toBe(true);
    });

    it('throws an exception when the customer is not found (404)', function (): void {
        $this->asaasClient->customer()->delete('cus_notfound');
    })->throws(NotFoundException::class, 'Resource not found');

    it('throws an exception when the customer ID is empty', function (): void {
        $this->asaasClient->customer()->delete('');
    })->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
