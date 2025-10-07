<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customer;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customer\CreateCustomerDTO;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;


final class Create extends AbstractAction
{
    /**
     * Create a new customer in Asaas
     *
     * @param  CreateCustomerDTO  $data  Customer data
     * @return array Customer data from API
     *
     */
    public function handle(CreateCustomerDTO $data): array
    {
        return $this->executeRequest(
            fn() => $this->client->post('customers', ['json' => $data->toArray()])
        );
    }
}
