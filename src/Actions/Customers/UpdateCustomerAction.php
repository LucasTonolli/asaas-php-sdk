<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customers\UpdateCustomerDTO;

final class UpdateCustomerAction extends AbstractAction
{
    public function handle(string $id, UpdateCustomerDTO $data): array
    {
        if (empty(trim($id))) {
            throw new \InvalidArgumentException('Customer ID cannot be empty');
        }

        return $this->executeRequest(
            fn () => $this->client->put('customers/'.rawurlencode($id), ['json' => $data->toArray()])
        );
    }
}
