<?php

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;

final class ListCustomersAction extends AbstractAction
{
    /**
     * Retrieves a paginated list of customers, with optional filters.
     *
     * This action sends a GET request to the 'customers' endpoint. All filtering
     * and pagination parameters are encapsulated in the ListCustomersDTO.
     *
     * @see https://docs.asaas.com/reference/listar-clientes Official Asaas API Documentation
     *
     * @param  ListCustomersDTO  $data A DTO containing filter and pagination parameters (e.g., name, email, limit, offset).
     * @return array A paginated list of customers. The structure includes pagination info and a 'data' key with the customers array.
     *
     * @throws \AsaasPhpSdk\Exceptions\ApiException
     * @throws \AsaasPhpSdk\Exceptions\AuthenticationException
     * @throws \AsaasPhpSdk\Exceptions\ValidationException Can be thrown if an invalid filter is sent.
     */
    public function handle(ListCustomersDTO $data): array
    {
        return $this->executeRequest(
            fn() => $this->client->get('customers', ['query' => $data->toArray()])
        );
    }
}
