<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Actions\Customers;

use AsaasPhpSdk\Actions\AbstractAction;

final class GetCustomerAction extends AbstractAction
{
	public function handle(string $id): array
	{
		return $this->executeRequest(
			fn() => $this->client->get("customers/{$id}")
		);
	}
}
