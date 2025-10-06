<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customer;

use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;
use AsaasPhpSdk\ValueObjects\Phone;
use AsaasPhpSdk\ValueObjects\PostalCode;
use AsaasPhpSdk\ValueObjects\ValueObjectContract;

final class CreateCustomerDTO
{
    private function __construct(
        public readonly string $name,
        public readonly Cpf|Cnpj $cpfCnpj,
        public readonly ?Email $email = null,
        public readonly ?Phone $phone = null,
        public readonly ?Phone $mobilePhone = null,
        public readonly ?string $address = null,
        public readonly ?string $addressNumber = null,
        public readonly ?string $complement = null,
        public readonly ?string $province = null,
        public readonly ?PostalCode $postalCode = null,
        public readonly ?string $externalReference = null,
        public readonly bool $notificationDisabled = false,
        public readonly ?string $additionalEmails = null,
        public readonly ?string $municipalInscription = null,
        public readonly ?string $stateInscription = null,
        public readonly ?string $observations = null,
        public readonly ?string $groupName = null,
        public readonly ?string $company = null,
        public readonly bool $foreignCustomer = false
    ) {}

    public static function fromArray(array $data): self
    {

        $sanitizedData = self::sanitize($data);
        $validatedData = self::validate($sanitizedData);


        return new self(
            ...$validatedData
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'cpfCnpj' => $this->cpfCnpj->value(),
            'email' => $this->email?->value(),
            'phone' => $this->phone?->value(),
            'mobilePhone' => $this->mobilePhone?->value(),
            'address' => $this->address,
            'addressNumber' => $this->addressNumber,
            'complement' => $this->complement,
            'province' => $this->province,
            'postalCode' => $this->postalCode?->formatted(),
            'externalReference' => $this->externalReference,
            'notificationDisabled' => $this->notificationDisabled,
            'additionalEmails' => $this->additionalEmails,
            'municipalInscription' => $this->municipalInscription,
            'stateInscription' => $this->stateInscription,
            'observations' => $this->observations,
            'groupName' => $this->groupName,
            'company' => $this->company,
            'foreignCustomer' => $this->foreignCustomer,
        ];

        return array_filter($data, fn($value) => $value !== null);
    }

    private static function sanitize(array $data): array
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
        if (empty($data['name'])) {
            throw InvalidCustomerDataException::missingField('name');
        }

        if (empty($data['cpfCnpj'])) {
            throw InvalidCustomerDataException::missingField('cpfCnpj');
        }

        try {
            $sanitized = DataSanitizer::onlyDigits($data['cpfCnpj']);
            $length = strlen($sanitized ?? '');

            $data['cpfCnpj'] = match ($length) {
                11 => Cpf::from($data['cpfCnpj']),
                14 => Cnpj::from($data['cpfCnpj']),
                default => throw new \InvalidArgumentException(
                    " CPF or CNPJ must contain 11 or 14 digits"
                ),
            };
        } catch (\Exception $e) {
            throw InvalidCustomerDataException::invalidFormat('cpfCnpj', $e->getMessage());
        }

        self::validateValueObject($data, 'email', Email::class);
        self::validateValueObject($data, 'postalCode', PostalCode::class);
        self::validateValueObject($data, 'phone', Phone::class);
        self::validateValueObject($data, 'mobilePhone', Phone::class);

        return $data;
    }

    private static function validateValueObject(array &$data, string $key, string $class): void
    {
        if (isset($data[$key])) {
            try {
                $data[$key] = $class::from($data[$key]);
            } catch (\Exception $e) {
                throw InvalidCustomerDataException::invalidFormat($key, $e->getMessage());
            }
        }
    }
}
