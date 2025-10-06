<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Exceptions\InvalidPhoneException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Traits\StringValueObject;

final class Phone implements FormattableContract, ValueObjectContract
{
	use StringValueObject;

	public static function from(string $phone): self
	{
		$sanitized = DataSanitizer::onlyDigits($phone);

		if ($sanitized === null) {
			throw new InvalidPhoneException('Phone number cannot be empty');
		}

		$length = strlen($sanitized);
		if ($length !== 10 && $length !== 11) {
			throw new InvalidPhoneException(
				"Phone must contain 10 or 11 digits"
			);
		}

		return new self($sanitized);
	}

	public function formatted(): string
	{
		if (strlen($this->value) === 11) {
			return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->value);
		}

		return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $this->value);
	}

	public function isMobile(): bool
	{
		return strlen($this->value) === 11;
	}

	public function isLandline(): bool
	{
		return strlen($this->value) === 10;
	}
}
