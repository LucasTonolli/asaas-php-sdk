<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Services;

use AsaasPhpSdk\Helper\HttpClientFactory;
use GuzzleHttp\Client;

final class AsaasServiceProvider
{
	public Client $httpClient;
	public function __construct(private string $token, private string $enviroment = 'sandbox')
	{
		$this->httpClient = HttpClientFactory::make($this->token, $this->enviroment);
	}
}
