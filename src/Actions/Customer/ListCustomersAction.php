<?php

namespace AsaasPhpSdk\Actions\Customer;

use AsaasPhpSdk\Actions\AbstractAction;
use AsaasPhpSdk\DTOs\Customer\ListCustomersDTO;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;


final class ListCustomersAction extends AbstractAction
{
	public function __construct(protected readonly Client $client, protected readonly ResponseHandler $responseHandler) {}
	public function handle(ListCustomersDTO $data): array
	{
		return $this->executeRequest(
			fn() => $this->client->get('customers', ['query' => $data->toArray()])
		);
	}
}
