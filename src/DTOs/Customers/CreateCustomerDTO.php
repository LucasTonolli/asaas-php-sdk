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

/**
 * A "Strict" Data Transfer Object for creating a new customer.
 *
 * This DTO validates input data rigorously upon creation through the `fromArray`
 * static method. It ensures that an instance of this class can only exist in a
 * valid state, throwing an `InvalidCustomerDataException` if the data is invalid.
 */
final class CreateCustomerDTO extends AbstractDTO
{
    /**
     * Private constructor to enforce object creation via the static `fromArray` factory method.
     *
     * @param  string  $name  The customer's full name.
     * @param  Cpf|Cnpj  $cpfCnpj  The customer's document (CPF or CNPJ) as a Value Object.
     * @param  ?Email  $email  The customer's primary email address as a Value Object.
     * @param  ?Phone  $phone  The customer's landline phone as a Value Object.
     * @param  ?Phone  $mobilePhone  The customer's mobile phone as a Value Object.
     * @param  ?string  $address  The street address.
     * @param  ?string  $addressNumber  The address number.
     * @param  ?string  $complement  Additional address information.
     * @param  ?string  $province  The neighborhood or province.
     * @param  ?PostalCode  $postalCode  The postal code as a Value Object.
     * @param  ?string  $externalReference  A unique external identifier for the customer.
     * @param  ?bool  $notificationDisabled  Disables notifications for the customer if true.
     * @param  ?string  $additionalEmails  A comma-separated list of additional notification emails.
     * @param  ?string  $municipalInscription  The municipal registration number.
     * @param  ?string  $stateInscription  The state registration number.
     * @param  ?string  $observations  Any observations about the customer.
     * @param  ?string  $groupName  The name of the group the customer belongs to.
     * @param  ?string  $company  The company name, if applicable.
     * @param  ?bool  $foreignCustomer  Indicates if the customer is foreign.
     */
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

    /**
     * Creates a new CreateCustomerDTO instance from a raw array of data.
     *
     * This factory method orchestrates the sanitization and validation of the
     * input data, ensuring the DTO is always created in a valid state.
     *
     * @param  array<string, mixed>  $data  Raw data, typically from an HTTP request or user input.
     * @return self A new, validated instance of the DTO.
     *
     * @throws InvalidCustomerDataException if the data is invalid (e.g., missing required fields, invalid format).
     */
    public static function fromArray(array $data): self
    {

        $sanitizedData = self::sanitize($data);
        $validatedData = self::validate($sanitizedData);

        return new self(
            ...$validatedData
        );
    }

    /**
     * Sanitizes the raw input data array.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The raw input data.
     * @return array<string, mixed> The sanitized data array.
     */
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

    /**
     * Validates the sanitized data and converts values to Value Objects.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The sanitized data array.
     * @return array<string, mixed> The validated data array with values converted to VOs.
     *
     * @throws InvalidCustomerDataException|InvalidValueObjectException
     */
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
            self::validateSimpleValueObject($data, 'email', Email::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('email', $e->getMessage());
        }

        try {
            self::validateSimpleValueObject($data, 'postalCode', PostalCode::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('postalCode', $e->getMessage());
        }

        try {
            self::validateSimpleValueObject($data, 'phone', Phone::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('phone', $e->getMessage());
        }

        try {
            self::validateSimpleValueObject($data, 'mobilePhone', Phone::class);
        } catch (InvalidValueObjectException $e) {
            throw InvalidCustomerDataException::invalidFormat('mobilePhone', $e->getMessage());
        }

        return $data;
    }
}
