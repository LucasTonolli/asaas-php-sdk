<?php

namespace AsaasPhpSdk\Actions;

use AsaasPhpSdk\Exceptions\ApiException;
use AsaasPhpSdk\Helpers\ResponseHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

abstract class AbstractAction
{
	public function __construct(
		protected readonly Client $client,
		protected readonly ResponseHandler $responseHandler
	) {}

	protected function executeRequest(callable $request): array
	{
		try {
			$response = $request();
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
