<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;

class Email implements ValueObjectContract
{
	private string $value;

	private function __construct(string $email)
	{
		$this->value = $email;
	}

	public static function from(string $email): self
	{
		$sanitized = DataSanitizer::sanitizeEmail($email);

		if (!filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
			throw new \AsaasPhpSdk\Exceptions\InvalidEmailException('Email is not valid');
		}

		return new self($sanitized);
	}

	public function value(): string
	{
		return $this->value;
	}

	public function jsonSerialize(): mixed
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return $this->value;
	}

	public function equals(ValueObjectContract $other): bool
	{
		return $other instanceof self && $this->value === $other->value;
	}
}
