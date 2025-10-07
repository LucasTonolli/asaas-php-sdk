<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;

final class CreateCustomerAction extends AbstractAction
{
    /**
     * Create a new customer in Asaas
     *
     * @param  CreateCustomerDTO  $data  Customer data
     * @return array Customer data from API
     */
    public function handle(CreateCustomerDTO $data): array
    {
        return $this->executeRequest(
            fn () => $this->client->post('customers', ['json' => $data->toArray()])
        );
    }
}
