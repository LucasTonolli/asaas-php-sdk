<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Traits\StringValueObject;

class PostalCode implements FormattableContract, ValueObjectContract
{
	use StringValueObject;

	public static function from(string $postalCode): self
	{
		$sanitized = DataSanitizer::onlyDigits($postalCode);

		if ($sanitized === null || strlen($sanitized) !== 8) {
			throw new \AsaasPhpSdk\Exceptions\InvalidPostalCodeException('Postal must contain exactly 8 digits');
		}

		if (!self::isValidPostalCode($sanitized)) {
			throw new \AsaasPhpSdk\Exceptions\InvalidPostalCodeException("Invalid Postal Code: {$postalCode}");
		}

		return new self($sanitized);
	}

	public static function isValidPostalCode(string $postalCode): bool
	{
		$postalCode = DataSanitizer::onlyDigits($postalCode) ?? '';

		if (strlen($postalCode) !== 8) {
			return false;
		}

		return true;
	}

	public function formatted(): string
	{
		return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->value);
	}
}
