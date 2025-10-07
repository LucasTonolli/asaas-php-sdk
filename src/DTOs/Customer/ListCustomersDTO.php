<?php

namespace AsaasPhpSdk\DTOs\Customer;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;

class ListCustomersDTO
{

	private function __construct(
		public readonly ?int $offset = null,
		public readonly ?int $limit = null,
		public readonly ?string $name = null,
		public readonly ?Email $email = null,
		public readonly Cpf|Cnpj|null $cpfCnpj = null,
		public readonly ?string $groupName = null,
		public readonly ?string $externalReference = null
	) {}
	public static function fromArray(array $data): self
	{
		$sanitizedData = self::sanitize($data);

		return new self(...$sanitizedData);
	}

	public function toArray(): array
	{
		return array_filter([
			'offset' => $this->offset,
			'limit' => $this->limit,
			'name' => $this->name,
			'email' => $this->email?->value(),
			'cpfCnpj' => $this->cpfCnpj?->value(),
			'groupName' => $this->groupName,
			'externalReference' => $this->externalReference
		], fn($value) => $value !== null);
	}

	private static function sanitize(array $data): array
	{
		return [
			'offset' => DataSanitizer::sanitizeInteger($data['offset']) ?? null,
			'limit' => DataSanitizer::sanitizeInteger($data['limit']) ?? null,
			'name' => DataSanitizer::sanitizeString($data['name']) ?? null,
			'email' => self::sanitizeEmail($data['email'] ?? null),
			'cpfCnpj' => self::sanitizeCpfCnpj($data['cpfCnpj'] ?? null),
			'groupName' => DataSanitizer::sanitizeString($data['groupName']) ?? null,
			'externalReference' => DataSanitizer::sanitizeString($data['externalReference']) ?? null
		];
	}

	private static function sanitizeEmail(?string $email): ?Email
	{
		if ($email === null) {
			return null;
		}

		try {
			return Email::from($email);
		} catch (\Exception) {
			return null;
		}
	}

	private static function sanitizeCpfCnpj(?string $cpfCnpj): Cpf|Cnpj|null
	{
		if ($cpfCnpj === null) {
			return null;
		}

		$sanitized = DataSanitizer::onlyDigits($cpfCnpj);
		if ($sanitized === null) {
			return null;
		}

		$lenght = strlen($sanitized);

		try {
			return match ($lenght) {
				11 => Cpf::from($sanitized),
				14 => Cnpj::from($sanitized),
				default => null
			};
		} catch (\Exception) {
			return null;
		}
	}
}
