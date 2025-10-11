<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Enums\FineType;

final class Interest
{
	public function __construct(
		public readonly int $value,
		public readonly FineType $type,
	) {}

	public static function create(float $value, FineType|string $type): self
	{
		if ($value < 0) {
			throw new \InvalidArgumentException('Fine value cannot be negative');
		}


		$type = FineType::tryFromString($type);

		if ($type === null) {
			throw new \InvalidArgumentException('Invalid fine type');
		}


		// Validate percentage
		if ($type === FineType::Percentage && $value > 100) {
			throw new \InvalidArgumentException('Fine percentage cannot exceed 100%');
		}

		return new self($value, $type);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			value: $data['value'] ?? throw new \InvalidArgumentException('Fine value is required'),
			type: $data['type'] ?? FineType::Percentage
		);
	}
}
