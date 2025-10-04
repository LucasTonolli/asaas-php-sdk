<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Config;

class AsaasConfig
{
    private string $token;

    private string $baseUrl;

    private bool $isSandbox;

    public function __construct(string $token, bool $isSandbox, ?string $customUrl = null)
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Asaas API token is required');
        }

        $this->token = $token;
        $this->isSandbox = $isSandbox;
        $this->baseUrl = $customUrl ?? $this->getDefaultUrl($isSandbox);
    }

    public static function fromEnvironment(bool $isSandbox): self
    {
        $tokenKey = $isSandbox ? 'ASAAS_SANDBOX_TOKEN' : 'ASAAS_PROD_TOKEN';
        $urlKey = $isSandbox ? 'ASAAS_SANDBOX_URL' : 'ASAAS_PROD_URL';

        $token = $_ENV[$tokenKey] ?? $_SERVER[$tokenKey] ?? null;

        if (! $token) {
            throw new \RuntimeException("Enviroment variable {$tokenKey} is not set");
        }

        $customUrl = $_ENV[$urlKey] ?? getenv($urlKey) ?: null;

        return new self($token, $isSandbox, $customUrl);
    }

    private function getDefaultUrl(bool $isSandbox): string
    {
        return $isSandbox ? 'https://sandbox.asaas.com/v3' : 'https://api.asaas.com/v3';
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function isSandbox(): bool
    {
        return $this->isSandbox;
    }
}
