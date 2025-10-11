<?php

declare(strict_types=1);

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Enums\DiscountType;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractStructuredValueObject;

final class Discount implements AbstractStructuredValueObject
{
	private function __construct(
		private readonly float $value,
		public readonly ?int $dueDateLimitDays,
		public readonly DiscountType $discountType
	) {}

	public static function create(float $value, ?int $dueDateLimitDays, DiscountType|string $discountType): self
	{
		if ($value <= 0) {
			throw new \InvalidArgumentException('Value must be greater than 0.');
		}

		$saninitizeddueDateLimitDays = DataSanitizer::sanitizeInteger($dueDateLimitDays);
		$value = DataSanitizer::sanitizeFloat($value);
		$discountType = DataSanitizer::sanitizeLowercase($discountType);
		$type = DiscountType::tryFromString($discountType);

		if ($type === null) {
			throw new \InvalidArgumentException('Invalid discount type');
		}

		return new self($value, $saninitizeddueDateLimitDays, $type);
	}

	public static function fromArray(array $data): self
	{
		return self::create(
			value: $data['value'] ?? throw new \InvalidArgumentException('Discount value is required'),
			dueDateLimitDays: $data['dueDateLimitDays'] ?? throw new \InvalidArgumentException('Discount dueDateLimitDays is required'),
			discountType: $data['type'] ?? DiscountType::Fixed
		);
	}

	public function calculateAmount(float $paymentValue): float
	{
		return match ($this->discountType) {
			DiscountType::Fixed => $this->value,
			DiscountType::Percentage => ($paymentValue * $this->value) / 100,
		};
	}
}
