<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customer;

use AsaasPhpSdk\DTOs\Customer\CreateCustomerDTO;
use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Helper\ResponseHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;

final class Create
{
    public function __construct(private readonly Client $client, private readonly ResponseHandler $responseHandler) {}

    /**
     * Create a new customer in Asaas
     *
     * @param  CreateCustomerDTO  $data  Customer data
     * @return array Customer data from API
     *
     * @throws AsaasException
     */
    public function handle(CreateCustomerDTO $data): array
    {
        try {
            $response = $this->client->post('customers', [
                'json' => $data->toArray(),
            ]);

            return $this->responseHandler->handle($response);
        } catch (ConnectException $e) {
            throw new ApiException('Failed to connect...');
        } catch (GuzzleException $e) {
            throw new ApiException('Failed to create customer...');
        }
    }
}
