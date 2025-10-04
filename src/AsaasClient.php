<?php

declare(strict_types=1);

namespace AsaasPhpSdk;

use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\Helper\HttpClientFactory;
use AsaasPhpSdk\Services\CustomerService;
use GuzzleHttp\Client;

final class AsaasClient
{
    private Client $httpClient;

    private ?CustomerService $customerService = null;

    public function __construct(private readonly AsaasConfig $config)
    {
        $this->httpClient = HttpClientFactory::make($this->config);
    }

    public function customer(): CustomerService
    {
        if ($this->customerService !== null) {
            return $this->customerService;
        }

        return new CustomerService($this->httpClient);
    }

    public function config(): AsaasConfig
    {
        return $this->config;
    }

    public function httpClient(): Client
    {
        return $this->httpClient;
    }

    public function isSandbox(): bool
    {
        return $this->config->isSandbox();
    }
}
