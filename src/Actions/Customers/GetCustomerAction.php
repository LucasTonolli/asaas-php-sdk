<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;

final class GetCustomerAction extends AbstractAction
{
	public function handle(string $id): array
	{
		if (empty(trim($id))) {
			throw new \InvalidArgumentException('Customer ID cannot be empty');
		}
		return $this->executeRequest(
			fn() => $this->client->get("customers/{$id}")
		);
	}
}
