<?php

declare(strict_types=1);

namespace AsaasPhpSdk;

use AsaasPhpSdk\Config\AsaasConfig;
use AsaasPhpSdk\Helpers\HttpClientFactory;
use AsaasPhpSdk\Services\CustomerService;
use AsaasPhpSdk\Services\PaymentService;
use GuzzleHttp\Client;

/**
 * The main entry point for interacting with the Asaas API.
 *
 * This class provides access to all the different services (e.g., Customer, Payment)
 * and manages the underlying HTTP client configuration.
 *
 * @example
 * // 1. Create a configuration object
 * $config = new AsaasPhpSdk\Config\AsaasConfig('YOUR_API_KEY', isSandbox: true);
 *
 * // 2. Instantiate the main client
 * $asaas = new AsaasPhpSdk\AsaasClient($config);
 *
 * // 3. Access a service and make a call
 * $allCustomers = $asaas->customer()->list();
 */
final class AsaasClient
{
    private Client $httpClient;

    private ?CustomerService $customerService = null;

    private ?PaymentService $paymentService = null;

    /**
     * AsaasClient constructor.
     *
     * @param  AsaasConfig  $config  The configuration object with API token and environment settings.
     *
     * @throws \InvalidArgumentException if the API token in the config is empty.
     */
    public function __construct(private readonly AsaasConfig $config)
    {
        $this->httpClient = HttpClientFactory::make($this->config);
    }

    /**
     * Gets the Customer service handler.
     *
     * The service is lazy-loaded: it is instantiated on the first call and the
     * same instance is reused for all subsequent calls.
     *
     * @return CustomerService An instance of the CustomerService.
     */
    public function customer(): CustomerService
    {
        if ($this->customerService !== null) {
            return $this->customerService;
        }
        $this->customerService = new CustomerService($this->httpClient);

        return $this->customerService;
    }

    public function payment(): PaymentService
    {
        if ($this->paymentService !== null) {
            return $this->paymentService;
        }
        $this->paymentService = new PaymentService($this->httpClient);

        return $this->paymentService;
    }

    /**
     * Gets the configuration object used by the client.
     */
    public function config(): AsaasConfig
    {
        return $this->config;
    }

    /**
     * Gets the underlying Guzzle HTTP client instance.
     *
     * This can be useful for advanced use cases, such as adding custom
     * middleware or inspecting requests/responses.
     *
     * @return Client The configured GuzzleHttp\Client instance.
     */
    public function httpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * Checks if the client is configured to use the sandbox environment.
     *
     * A convenience proxy method for `$client->config()->isSandbox()`.
     */
    public function isSandbox(): bool
    {
        return $this->config->isSandbox();
    }
}
