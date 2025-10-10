<?php

describe('Update Customer', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('updates a customer successfully', function (): void {
        $createCustomerResponse = $this->asaasClient->customer()->create([
            'name' => 'John Doe'.uniqid(),
            'email' => 'john_doe'.uniqid().'@example.com',
            'cpfCnpj' => '898.879.660-88',
        ]);

        $response = $this->asaasClient->customer()->update($createCustomerResponse['id'], [
            'name' => 'John Doe Updated',
            'email' => 'john_doe_updated@example.com',
        ]);
        expect($response['name'])->toBe('John Doe Updated')
            ->and($response['email'])->toBe('john_doe_updated@example.com')
            ->and($response)->toHaveKeys(CUSTOMER_KEYS);
    });

    it('throws an exception when the customer is not found (404)', function (): void {
        $this->asaasClient->customer()->update('cus_notfound', [
            'name' => 'John Doe Updated',
        ]);
    })->throws(\AsaasPhpSdk\Exceptions\NotFoundException::class, 'Resource not found');

    it('throws an exception when the customer ID is empty', function (): void {
        $this->asaasClient->customer()->update('', [
            'name' => 'John Doe Updated',
        ]);
    })->throws(\InvalidArgumentException::class, 'Customer ID cannot be empty');
});
