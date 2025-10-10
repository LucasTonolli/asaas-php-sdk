<?php

describe('Create Customer', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('creates a customer successfully', function (): void {
        $name = 'John Doe '.uniqid();

        $response = $this->asaasClient->customer()->create([
            'name' => $name,
            'cpfCnpj' => '898.879.660-88',
        ]);
        expect($response['id'])->not()->toBeEmpty()
            ->and($response['name'])->toBe($name)
            ->and($response['cpfCnpj'])->toBe('89887966088');
    });

    it('fails to create a customer with invalid cpf', function (): void {
        expect(fn () => $this->asaasClient->customer()->create([
            'name' => 'Invalid CPF',
            'cpfCnpj' => '000.000.000-00',
        ]))->toThrow(\AsaasPhpSdk\Exceptions\ValidationException::class, 'Invalid CPF: 000.000.000-00');
    });

    it('matches the expected response structure', function (): void {
        $response = $this->asaasClient->customer()->create([
            'name' => 'Snapshot Test',
            'cpfCnpj' => '898.879.660-88',
        ]);
        expect($response)->toHaveKeys([
            'object',
            'id',
            'dateCreated',
            'name',
            'email',
            'phone',
            'mobilePhone',
            'address',
            'addressNumber',
            'complement',
            'province',
            'city',
            'cityName',
            'state',
            'country',
            'postalCode',
            'cpfCnpj',
            'personType',
            'deleted',
            'additionalEmails',
            'externalReference',
            'notificationDisabled',
            'observations',
        ]);

        expect($response['object'])->toBe('customer')
            ->and($response['id'])->toStartWith('cus_')
            ->and($response['cpfCnpj'])->toMatch('/^\d{11}|\d{14}$/')
            ->and($response['personType'])->toBe('FISICA')
            ->and($response['deleted'])->toBeBool();
    });
});
