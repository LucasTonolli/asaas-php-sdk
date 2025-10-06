<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Traits\StringValueObject;

class Phone implements FormattableContract, ValueObjectContract
{
	use StringValueObject;

	public static function from(string $phone): self
	{
		$sanitized = DataSanitizer::onlyDigits($phone);

		if ($sanitized === null || strlen($sanitized) !== 11) {
			throw new \AsaasPhpSdk\Exceptions\InvalidPhoneException('Invalid phone number format');
		}

		return new self($sanitized);
	}
	public function formatted(): string
	{
		return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->value);
	}
}
