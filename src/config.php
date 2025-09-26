<?php

return [
	'PROD_TOKEN' => getenv('ASAAS_PROD_TOKEN'),
	'SANDBOX_TOKEN' => getenv('ASAAS_SANDBOX_TOKEN'),
	'PROD_URL' => getenv('ASAAS_PROD_URL') ?? 'https://api.asaas.com/v3',
	'SANDBOX_URL' => getenv('ASAAS_SANDBOX_URL') ?? 'https://sandbox.asaas.com/v3'
];
