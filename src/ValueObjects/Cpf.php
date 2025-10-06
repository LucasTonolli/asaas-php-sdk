<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helper\DataSanitizer;

class Cpf implements FormattableContract, ValueObjectContract
{
	private string $value;

	private function __construct(string $cpf)
	{
		$this->value = $cpf;
	}

	public static function from(string $cpf): self
	{
		$sanitized = DataSanitizer::onlyNumbers($cpf);

		if ($sanitized === null || strlen($sanitized) !== 11) {
			throw new \AsaasPhpSdk\Exceptions\InvalidCpfException('CPF must contain exactly 11 digits');
		}

		if (!self::isValidCpf($sanitized)) {
			throw new \AsaasPhpSdk\Exceptions\InvalidCpfException("Invalid CPF: {$cpf}");
		}

		return new self($sanitized);
	}

	public static function isValidCpf(string $cpf): bool
	{
		$cpf = DataSanitizer::onlyNumbers($cpf) ?? '';

		if (strlen($cpf) !== 11) {
			return false;
		}

		if (preg_match('/^(\d)\1{10}$/', $cpf)) {
			return false;
		}

		$sum = 0;
		for ($i = 0; $i < 9; $i++) {
			$sum += ((int) $cpf[$i]) * (10 - $i);
		}
		$digit1 = 11 - ($sum % 11);
		$digit1 = $digit1 >= 10 ? 0 : $digit1;

		if ($digit1 !== (int) $cpf[9]) {
			return false;
		}

		$sum = 0;
		for ($i = 0; $i < 10; $i++) {
			$sum += ((int) $cpf[$i]) * (11 - $i);
		}
		$digit2 = 11 - ($sum % 11);
		$digit2 = $digit2 >= 10 ? 0 : $digit2;

		return $digit2 === (int) $cpf[10];
	}


	public function value(): string
	{
		return $this->value;
	}

	public function formatted(): string
	{
		return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->value);
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
