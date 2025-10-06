<?php

describe('Test connection', function () {
	it('throws authentication exception with invalid token', function () {
		$config = new AsaasPhpSdk\Config\AsaasConfig(
			token: 'invalid_sandbox_token',
			isSandbox: true
		);
		$client = new AsaasPhpSdk\AsaasClient($config);

		expect(fn() => $client->customer()->create([
			'name' => 'Auth Fail',
			'cpfCnpj' => '898.879.660-88',
		]))->toThrow(AsaasPhpSdk\Exceptions\AuthenticationException::class);
	});
});
