<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use GuzzleHttp\Client;
use AsaasPhpSdk\Actions\Customer\Create as CreateCustomer;
use AsaasPhpSdk\DTOs\Customer\CreateCustomerDTO;
use AsaasPhpSdk\Exceptions\ValidationException;
use AsaasPhpSdk\Helper\ResponseHandler;

final class CustomerService
{

	public function __construct(private Client $client,  private readonly ResponseHandler $responseHandler = new ResponseHandler()) {}

	/**
	 * Create a new customer
	 * @param array $data Customer data
	 * @return array Customer data from Asaas
	 * @throws ValidationException
	 */

	public function create(array $data): array
	{
		try {
			$customerDTO = CreateCustomerDTO::fromArray($data);
		} catch (\AsaasPhpSdk\Exceptions\InvalidCustomerDataException $e) {
			throw new ValidationException($e->getMessage(), $e->getCode(), $e);
		}

		$action = new CreateCustomer($this->client, $this->responseHandler);
		return $action->handle($customerDTO);
	}
}
