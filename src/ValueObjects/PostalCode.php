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

		return new self($sanitized);
	}

	public function formatted(): string
	{
		return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->value);
	}
}
