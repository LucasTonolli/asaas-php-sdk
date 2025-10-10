<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\UpdateCustomerDTO;

final class UpdateCustomerAction extends AbstractAction
{
    /**
     * Updates an existing customer by their ID.
     *
     * This action performs a pre-request validation on the ID and sends a PUT
     * request to the 'customers/{id}' endpoint with the new data.
     *
     * @see https://docs.asaas.com/reference/atualizar-cliente-existente Official Asaas API Documentation
     *
     * @param  string  $id  The unique identifier of the customer to be updated.
     * @param  UpdateCustomerDTO  $data  A DTO containing the customer data to be updated.
     * @return array An array containing the full, updated data of the customer.
     *
     * @throws \InvalidArgumentException if the provided customer ID is empty.
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\NotFoundException if the customer with the given ID does not exist.
     * @throws \AsaasPhpSdk\Exceptions\ValidationException if the data provided is invalid.
     */
    public function handle(string $id, UpdateCustomerDTO $data): array
    {
        $normalizedId = trim($id);
        if ($normalizedId === '') {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->put('customers/'.rawurlencode($normalizedId), ['json' => $data->toArray()])
        );
    }
}
