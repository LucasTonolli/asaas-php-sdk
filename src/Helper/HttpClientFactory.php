<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Helper;

use GuzzleHttp\Client;

final class HttpClientFactory
{

	public static function make(string $token, string $enviroment = 'sandbox')
	{
		return new Client([
			'base_uri' => $enviroment === 'sandbox' ? getenv('SANDBOX_URL') : getenv('PROD_URL'),
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
				'access_token' => $token
			]
		]);
	}
}
