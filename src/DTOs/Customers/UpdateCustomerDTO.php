<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customers;

use AsaasPhpSdk\DTOs\AbstractDTO;
use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;
use AsaasPhpSdk\ValueObjects\Phone;
use AsaasPhpSdk\ValueObjects\PostalCode;
use InvalidArgumentException;

final class UpdateCustomerDTO extends AbstractDTO
{
	private function __construct(
		public readonly ?string $name,
		public readonly null|Cpf|Cnpj $cpfCnpj,
		public readonly ?Email $email = null,
		public readonly ?Phone $phone = null,
		public readonly ?Phone $mobilePhone = null,
		public readonly ?string $address = null,
		public readonly ?string $addressNumber = null,
		public readonly ?string $complement = null,
		public readonly ?string $province = null,
		#[ToArrayMethodAttribute('formatted')]
		public readonly ?PostalCode $postalCode = null,
		public readonly ?string $externalReference = null,
		public readonly ?bool $notificationDisabled = null,
		public readonly ?string $additionalEmails = null,
		public readonly ?string $municipalInscription = null,
		public readonly ?string $stateInscription = null,
		public readonly ?string $observations = null,
		public readonly ?string $groupName = null,
		public readonly ?string $company = null,
		public readonly ?bool $foreignCustomer = null
	) {}

	public static function fromArray(array $data): self
	{
		$sanitizedData = self::sanitize($data);
		$validatedData = self::validate($sanitizedData);

		return new self(...$validatedData);
	}

	protected static function sanitize(array $data): array
	{
		return [
			'name' => DataSanitizer::sanitizeString($data['name'] ?? ''),
			'cpfCnpj' => $data['cpfCnpj'] ?? null,
			'email' => DataSanitizer::sanitizeString($data['email'] ?? null),
			'phone' => $data['phone'] ?? null,
			'mobilePhone' => $data['mobilePhone'] ?? null,
			'address' => DataSanitizer::sanitizeString($data['address'] ?? null),
			'addressNumber' => DataSanitizer::sanitizeString($data['addressNumber'] ?? null),
			'complement' => DataSanitizer::sanitizeString($data['complement'] ?? null),
			'province' => DataSanitizer::sanitizeString($data['province'] ?? $data['neighborhood'] ?? null),
			'postalCode' => $data['postalCode'] ?? null,
			'externalReference' => DataSanitizer::sanitizeString($data['externalReference'] ?? null),
			'notificationDisabled' => DataSanitizer::sanitizeBoolean($data['notificationDisabled'] ?? false),
			'additionalEmails' => DataSanitizer::sanitizeString($data['additionalEmails'] ?? null),
			'municipalInscription' => DataSanitizer::sanitizeString($data['municipalInscription'] ?? null),
			'stateInscription' => DataSanitizer::sanitizeString($data['stateInscription'] ?? null),
			'observations' => DataSanitizer::sanitizeString($data['observations'] ?? null),
			'groupName' => DataSanitizer::sanitizeString($data['groupName'] ?? null),
			'company' => DataSanitizer::sanitizeString($data['company'] ?? null),
			'foreignCustomer' => DataSanitizer::sanitizeBoolean($data['foreignCustomer'] ?? false),
		];
	}

	private static function validate(array $data): array
	{



		try {
			if (!($data['cpfCnpj'] instanceof Cpf || $data['cpfCnpj'] instanceof Cnpj)) {
				if ($data['cpfCnpj'] !== null) {
					$sanitized = DataSanitizer::onlyDigits($data['cpfCnpj']);
					$length = strlen($sanitized ?? '');

					$data['cpfCnpj'] = match ($length) {
						11 => Cpf::from($data['cpfCnpj']),
						14 => Cnpj::from($data['cpfCnpj']),
						default => throw new InvalidArgumentException('CPF or CNPJ must contain 11 or 14 digits'),
					};
				}
			}

			self::validateValueObject($data, 'email', Email::class);
			self::validateValueObject($data, 'postalCode', PostalCode::class);
			self::validateValueObject($data, 'phone', Phone::class);
			self::validateValueObject($data, 'mobilePhone', Phone::class);
		} catch (InvalidArgumentException $e) {
			throw new InvalidCustomerDataException($e->getMessage(), 0, $e);
		}

		return $data;
	}
}
