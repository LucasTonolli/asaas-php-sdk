<?php

namespace AsaasPhpSdk\Actions\Customer;

use AsaasPhpSdk\DTOs\Customer\ListCustomersDTO;
use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

final class ListCustomersAction
{
	public function __construct(private readonly Client $client, private readonly ResponseHandler $responseHandler) {}


	public function handle(ListCustomersDTO $data): array
	{
		try {
			$response = $this->client->get('customers', [
				'query' => $data->toArray(),
			]);

			return $this->responseHandler->handle($response);
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				return $this->responseHandler->handle($e->getResponse());
			}

			throw new ApiException(
				'Request failed: ' . $e->getMessage(),
				$e->getCode(),
				$e
			);
		} catch (ConnectException $e) {
			throw new ApiException(
				'Failed to connect to Asaas API: ' . $e->getMessage(),
				0,
				$e
			);
		} catch (GuzzleException $e) {
			throw new ApiException(
				'HTTP client error: ' . $e->getMessage(),
				$e->getCode(),
				$e
			);
		}
	}
}
