<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;

class Phone implements FormattableContract, ValueObjectContract
{
	private string $value;

	private function __construct(string $phone)
	{
		$this->value = $phone;
	}

	public static function from(string $phone): self
	{
		$sanitized = DataSanitizer::onlyDigits($phone);

		if ($sanitized === null || strlen($sanitized) !== 11) {
			throw new \AsaasPhpSdk\Exceptions\InvalidPhoneException('Invalid phone number format');
		}

		return new self($sanitized);
	}

	public function value(): string
	{
		return $this->value;
	}

	public function formatted(): string
	{
		return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->value);
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
