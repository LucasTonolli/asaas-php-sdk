<?php

use AsaasPhpSdk\Actions\Customers\CreateCustomerAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;
use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Exception\ConnectException;

describe('Create Customer Action', function (): void {

    it('creates customer successfully', function (): void {
        $client = mockClient([
            mockResponse([
                'id' => 'cus_123',
                'name' => 'João Silva',
                'cpfCnpj' => '89887966088',
            ], 201),
        ]);

        $action = new CreateCustomerAction($client, new ResponseHandler);

        $dto = CreateCustomerDTO::fromArray([
            'name' => 'João Silva',
            'cpfCnpj' => '898.879.660-88',
        ]);

        $result = $action->handle($dto);

        expect($result)->toBeArray()
            ->and($result['id'])->toBe('cus_123')
            ->and($result['name'])->toBe('João Silva')
            ->and($result['cpfCnpj'])->toBe('89887966088');
    });

    it('throws ValidationException on 400 error', function (): void {
        $client = mockClient([
            mockErrorResponse('Input validation failed', 400, [
                ['description' => 'CPF is invalid'],
            ]),
        ]);
        $action = new CreateCustomerAction($client, new ResponseHandler);

        $dto = CreateCustomerDTO::fromArray([
            'name' => 'João Silva',
            'cpfCnpj' => '11144477735',
        ]);

        $action->handle($dto);
    })->throws(ValidationException::class, 'CPF is invalid');

    it('throws ApiException on network connection error', function (): void {
        $mock = new GuzzleHttp\Handler\MockHandler([
            new ConnectException(
                'Connection failed',
                new GuzzleHttp\Psr7\Request('POST', 'customers')
            ),
        ]);

        $handlerStack = GuzzleHttp\HandlerStack::create($mock);
        $client = new GuzzleHttp\Client(['handler' => $handlerStack]);

        $action = new CreateCustomerAction($client, new ResponseHandler);

        $dto = CreateCustomerDTO::fromArray([
            'name' => 'João Silva',
            'cpfCnpj' => '11144477735',
        ]);

        $action->handle($dto);
    })->throws(ApiException::class, 'Failed to connect to Asaas API: Connection failed');
});
