<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Config;

/**
 * Configuration object for the Asaas SDK.
 *
 * This class holds all the necessary settings to configure the SDK client,
 * such as the API token and the operating environment (sandbox or production).
 */
class AsaasConfig
{
    private string $token;

    private string $baseUrl;

    private bool $isSandbox;

    private bool $logsEnabled = false;

    /**
     * AsaasConfig constructor.
     *
     * @param  string  $token  Your Asaas API token.
     * @param  bool  $isSandbox  Set to true for sandbox mode, false for production.
     * @param  bool  $logsEnabled  Enable/disable HTTP request logging (useful for debugging in sandbox).
     * @param  ?string  $customUrl  Optionally override the default Asaas API base URL.
     *
     * @throws \InvalidArgumentException if the API token is empty.
     */
    public function __construct(string $token, bool $isSandbox, bool $logsEnabled = false, ?string $customUrl = null)
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Asaas API token is required');
        }

        $this->token = $token;
        $this->isSandbox = $isSandbox;
        $this->logsEnabled = $logsEnabled;
        $this->baseUrl = $customUrl ?? $this->getDefaultUrl($isSandbox);
    }

    /**
     * Creates a configuration instance from environment variables.
     *
     * This factory method looks for specific environment variables to configure the SDK.
     * - For sandbox: ASAAS_SANDBOX_TOKEN and optionally ASAAS_SANDBOX_URL.
     * - For production: ASAAS_PROD_TOKEN and optionally ASAAS_PROD_URL.
     *
     * @param  bool  $isSandbox  Specifies which set of environment variables to use.
     * @param  bool  $logsEnabled  Enable/disable HTTP request logging.
     * @return self A new configuration instance.
     *
     * @throws \RuntimeException if the required token environment variable is not set.
     */
    public static function fromEnvironment(bool $isSandbox, bool $logsEnabled): self
    {
        $tokenKey = $isSandbox ? 'ASAAS_SANDBOX_TOKEN' : 'ASAAS_PROD_TOKEN';
        $urlKey = $isSandbox ? 'ASAAS_SANDBOX_URL' : 'ASAAS_PROD_URL';

        $token =  $_ENV[$tokenKey] ?? $_SERVER[$tokenKey] ?? (getenv($tokenKey) ?: null);

        if (! $token) {
            throw new \RuntimeException("Environment variable {$tokenKey} is not set");
        }

        $customUrl = $_ENV[$urlKey] ?? getenv($urlKey) ?: null;

        return new self($token, $isSandbox, $logsEnabled, $customUrl);
    }

    /**
     * Gets the default Asaas API URL based on the environment.
     *
     * @internal
     */
    private function getDefaultUrl(bool $isSandbox): string
    {
        return $isSandbox ? 'https://api-sandbox.asaas.com/v3/' : 'https://api.asaas.com/v3/';
    }

    /**
     * Gets the configured API token.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Gets the configured API base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Checks if the configuration is for the sandbox environment.
     */
    public function isSandbox(): bool
    {
        return $this->isSandbox;
    }

    /**
     * Checks if logging is enabled.
     */
    public function isLogsEnabled(): bool
    {
        return $this->logsEnabled;
    }
}
