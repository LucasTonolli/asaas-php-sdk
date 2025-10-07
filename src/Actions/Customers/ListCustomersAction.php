<?php

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\ListCustomersDTO;

final class ListCustomersAction extends AbstractAction
{
    public function handle(ListCustomersDTO $data): array
    {
        return $this->executeRequest(
            fn () => $this->client->get('customers', ['query' => $data->toArray()])
        );
    }
}
