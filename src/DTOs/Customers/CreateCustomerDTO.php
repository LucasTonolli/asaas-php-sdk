<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customers;

use AsaasPhpSdk\DTOs\AbstractDTO;
use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;
use AsaasPhpSdk\Exceptions\InvalidValueObjectException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;
use AsaasPhpSdk\ValueObjects\Phone;
use AsaasPhpSdk\ValueObjects\PostalCode;


final class CreateCustomerDTO extends AbstractDTO
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

        return new self(
            ...$validatedData
        );
    }

    protected static function sanitize(array $data): array
    {
        return [
            'name' => DataSanitizer::sanitizeString($data['name'] ?? ''),
            'cpfCnpj' => $data['cpfCnpj'] ?? null,
            'email' => self::optionalString($data, 'email'),
            'phone' => self::optionalOnlyDigits($data, 'phone'),
            'mobilePhone' => self::optionalOnlyDigits($data, 'mobilePhone'),
            'address' => self::optionalString($data, 'address'),
            'addressNumber' => self::optionalString($data, 'addressNumber'),
            'complement' => self::optionalString($data, 'complement'),
            'province' => self::optionalString($data, 'province')
                ?? self::optionalString($data, 'neighborhood'),
            'postalCode' => self::optionalOnlyDigits($data, 'postalCode'),
            'externalReference' => self::optionalString($data, 'externalReference'),
            'notificationDisabled' => self::optionalBoolean($data, 'notificationDisabled'),
            'additionalEmails' => self::optionalString($data, 'additionalEmails'),
            'municipalInscription' => self::optionalString($data, 'municipalInscription'),
            'stateInscription' => self::optionalString($data, 'stateInscription'),
            'observations' => self::optionalString($data, 'observations'),
            'groupName' => self::optionalString($data, 'groupName'),
            'company' => self::optionalString($data, 'company'),
            'foreignCustomer' => self::optionalBoolean($data, 'foreignCustomer'),
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
                default => throw new InvalidValueObjectException(
                    'CPF or CNPJ must contain 11 or 14 digits'
                ),
            };
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('cpfCnpj', $e->getMessage());
        }

        try {
            self::validateValueObject($data, 'email', Email::class);
            self::validateValueObject($data, 'postalCode', PostalCode::class);
            self::validateValueObject($data, 'phone', Phone::class);
            self::validateValueObject($data, 'mobilePhone', Phone::class);
        } catch (InvalidValueObjectException  $e) {
            throw InvalidCustomerDataException::invalidFormat('customer data', $e->getMessage());
        }

        return $data;
    }
}
