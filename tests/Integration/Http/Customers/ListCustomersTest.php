<?php

describe('List Customers', function (): void {
    beforeEach(function (): void {
        $config = sandboxConfig();
        $this->asaasClient = new AsaasPhpSdk\AsaasClient($config);
    });

    it('lists customers successfully', function (): void {
        $response = $this->asaasClient->customer()->list([
            'limit' => 5,
            'offset' => 0,
        ]);
        expect($response)->not()->toBeEmpty()
            ->and($response['object'])->toBe('list')
            ->and($response)->toHaveKey('totalCount')
            ->and($response['limit'])->toBe(5)
            ->and($response['offset'])->toBe(0)
            ->and($response)->toHaveKey('data');
    });

    it('filters customers by name', function (): void {
        $response = $this->asaasClient->customer()->list([
            'limit' => 5,
            'name' => 'John Doe',
        ]);

        expect($response)->not()->toBeEmpty()
            ->and($response['object'])->toBe('list')
            ->and($response)->toHaveKey('totalCount')
            ->and($response['limit'])->toBe(5)
            ->and($response['offset'])->toBe(0)
            ->and($response)->toHaveKey('data');
    });

    it('matches the expected response structure', function (): void {
        $response = $this->asaasClient->customer()->list([
            'limit' => 5,
            'offset' => 0,
        ]);

        expect($response)->toHaveKeys([
            'object',
            'totalCount',
            'limit',
            'offset',
            'hasMore',
            'data',
        ]);
    });
});
