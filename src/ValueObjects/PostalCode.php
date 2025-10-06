<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helper\DataSanitizer;

class PostalCode implements FormattableContract, ValueObjectContract
{
	private string $value;

	private function __construct(string $postalCode)
	{
		$this->value = $postalCode;
	}

	public static function from(string $postalCode): self
	{
		$sanitized = DataSanitizer::onlyNumbers($postalCode);

		if ($sanitized === null || strlen($sanitized) !== 8) {
			throw new \AsaasPhpSdk\Exceptions\InvalidPostalCodeException('Postal must contain exactly 8 digits');
		}

		if (!self::isValidPostalCode($sanitized)) {
			throw new \AsaasPhpSdk\Exceptions\InvalidCpfException("Invalid Postal Code: {$postalCode}");
		}

		return new self($sanitized);
	}

	public static function isValidPostalCode(string $postalCode): bool
	{
		$postalCode = DataSanitizer::onlyNumbers($postalCode) ?? '';

		if (strlen($postalCode) !== 8) {
			return false;
		}

		/**
		 * TODO: Request to API VIACEP || BRASIL API
		 */
		return true;
	}


	public function value(): string
	{
		return $this->value;
	}

	public function formatted(): string
	{
		return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->value);
	}

	public function jsonSerialize(): mixed
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return $this->formatted();
	}
	public function equals(ValueObjectContract $other): bool
	{
		return $other instanceof self && $this->value === $other->value;
	}
}
