<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Interest extends AbstractStructuredValueObject
{
	public function __construct(
		public readonly float $value,
	) {}

	public static function create(float $value): self
	{
		if ($value < 0) {
			throw new \InvalidArgumentException('Interest value cannot be negative');
		}

		if ($value > 100) {
			throw new \InvalidArgumentException('Interest value cannot exceed 100%');
		}

		return new self($value);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			value: $data['value'] ?? throw new \InvalidArgumentException('Interest value is required')
		);
	}
}
