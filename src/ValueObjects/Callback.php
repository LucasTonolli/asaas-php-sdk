<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Callback extends AbstractStructuredValueObject
{
	private function __construct(
		public readonly string $successrl,
		public readonly bool $autoRedirect = true
	) {}

	public static function create(string $successUrl, bool $autoRedirect = true): self
	{
		// Validate URL format
		if (!filter_var($successUrl, FILTER_VALIDATE_URL)) {
			throw new \InvalidArgumentException("Invalid success URL: {$successUrl}");
		}

		// Validate HTTPS for security
		if (!str_starts_with($successUrl, 'https://')) {
			throw new \InvalidArgumentException('Success URL must use HTTPS protocol');
		}

		return new self($successUrl, $autoRedirect);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			successUrl: $data['successUrl'] ?? throw new \InvalidArgumentException('successUrl is required'),
			autoRedirect: $data['autoRedirect'] ?? true
		);
	}
}
