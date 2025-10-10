<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\CreateCustomerDTO;

final class CreateCustomerAction extends AbstractAction
{
    /**
     * Creates a new customer.
     *
     * This action sends a POST request to the 'customers' endpoint. The data is
     * encapsulated and validated by the CreateCustomerDTO before being sent.
     *
     * @see https://docs.asaas.com/reference/criar-novo-cliente Official Asaas API Documentation
     *
     * @param  CreateCustomerDTO  $data  A Data Transfer Object containing the validated customer data.
     * @return array An array representing the newly created customer as returned by the API.
     *
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\ValidationException
     */
    public function handle(CreateCustomerDTO $data): array
    {
        return $this->executeRequest(
            fn () => $this->client->post('customers', ['json' => $data->toArray()])
        );
    }
}
