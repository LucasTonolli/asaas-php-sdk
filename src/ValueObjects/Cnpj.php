<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;

class Cnpj implements FormattableContract, ValueObjectContract
{
	private string $value;

	private function __construct(string $cnpj)
	{
		$this->value = $cnpj;
	}

	public static function from(string $cnpj): self
	{
		$sanitized = DataSanitizer::onlyDigits($cnpj);

		if ($sanitized === null || strlen($sanitized) !== 14) {
			throw new \AsaasPhpSdk\Exceptions\InvalidCnpjException('Cnpj must contain exactly 14 digits');
		}

		if (!self::isValidCnpj($sanitized)) {
			throw new \AsaasPhpSdk\Exceptions\InvalidCnpjException("Invalid Cnpj: {$cnpj}");
		}

		return new self($sanitized);
	}

	public static function isValidCnpj(string $cnpj): bool
	{
		$cnpj = DataSanitizer::onlyDigits($cnpj) ?? '';

		if (strlen($cnpj) !== 14) {
			return false;
		}

		if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
			return false;
		}

		$weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
		$sum = 0;
		for ($i = 0; $i < 12; $i++) {
			$sum += ((int) $cnpj[$i]) * $weights[$i];
		}
		$digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

		if ($digit1 !== (int) $cnpj[12]) {
			return false;
		}

		$weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
		$sum = 0;
		for ($i = 0; $i < 13; $i++) {
			$sum += ((int) $cnpj[$i]) * $weights[$i];
		}
		$digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

		return $digit2 === (int) $cnpj[13];
	}


	public function value(): string
	{
		return $this->value;
	}

	public function formatted(): string
	{
		return preg_replace(
			'/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
			'$1.$2.$3/$4-$5',
			$this->value
		);
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
