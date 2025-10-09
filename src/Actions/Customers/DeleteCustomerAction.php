<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;

final class DeleteCustomerAction extends AbstractAction
{
    /**
     * Deletes an existing customer by their ID.
     *
     * This action performs a pre-request validation to ensure the ID is not
     * empty and then sends a DELETE request to the 'customers/{id}' endpoint.
     *
     * @see https://docs.asaas.com/reference/remover-cliente Official Asaas API Documentation
     *
     * @param  string  $id The unique identifier of the customer to be deleted.
     * @return array An array confirming the deletion, typically containing a 'deleted' flag.
     *
     * @throws \InvalidArgumentException if the provided customer ID is empty.
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\NotFoundException if the customer with the given ID does not exist.
     */
    public function handle(string $id): array
    {
        if (empty(trim($id))) {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn() => $this->client->delete('customers/' . rawurlencode($id))
        );
    }
}
