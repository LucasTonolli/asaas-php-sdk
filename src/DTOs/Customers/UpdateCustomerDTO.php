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
 * A "Strict" Data Transfer Object for updating an existing customer.
 *
 * This DTO is designed for partial updates, meaning all its properties are
 * optional. However, any data that is provided will be strictly validated.
 * An `InvalidCustomerDataException` is thrown if any of the provided fields
 * are malformed.
 */
final class UpdateCustomerDTO extends AbstractDTO
{
    /**
     * UpdateCustomerDTO private constructor.
     *
     * @param  ?string  $name  The customer's new full name.
     * @param  null|Cpf|Cnpj  $cpfCnpj  The customer's new document (CPF or CNPJ).
     * @param  ?Email  $email  The customer's new primary email address.
     * @param  ?Phone  $phone  The customer's new landline phone.
     * @param  ?Phone  $mobilePhone  The customer's new mobile phone.
     * @param  ?string  $address  The new street address.
     * @param  ?string  $addressNumber  The new address number.
     * @param  ?string  $complement  New additional address information.
     * @param  ?string  $province  The new neighborhood or province.
     * @param  ?PostalCode  $postalCode  The new postal code.
     * @param  ?string  $externalReference  A new unique external identifier.
     * @param  ?bool  $notificationDisabled  New setting to disable notifications.
     * @param  ?string  $additionalEmails  A new comma-separated list of additional emails.
     * @param  ?string  $municipalInscription  The new municipal registration number.
     * @param  ?string  $stateInscription  The new state registration number.
     * @param  ?string  $observations  New observations about the customer.
     * @param  ?string  $groupName  The new name of the customer's group.
     * @param  ?string  $company  The new company name.
     * @param  ?bool  $foreignCustomer  The new setting for foreign customer status.
     */
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

    /**
     * Creates a new UpdateCustomerDTO instance from a raw array of data.
     *
     * This factory method takes a raw array of data to be updated. It sanitizes
     * and validates only the fields that are provided in the array.
     *
     * @param  array<string, mixed>  $data  Raw data for the fields to be updated.
     * @return self A new, validated instance of the DTO.
     *
     * @throws InvalidCustomerDataException if any of the provided data is malformed.
     */
    public static function fromArray(array $data): self
    {
        $sanitizedData = self::sanitize($data);
        $validatedData = self::validate($sanitizedData);

        return new self(...$validatedData);
    }

    /**
     * Sanitizes the raw input data array for the update operation.
     *
     * @internal
     */
    protected static function sanitize(array $data): array
    {
        return [
            'name' => self::optionalString($data, 'name'),
            'cpfCnpj' => $data['cpfCnpj'] ?? null,
            'email' => self::optionalString($data, 'email'),
            'phone' => $data['phone'] ?? null,
            'mobilePhone' => $data['mobilePhone'] ?? null,
            'address' => self::optionalString($data, 'address'),
            'addressNumber' => self::optionalString($data, 'addressNumber'),
            'complement' => self::optionalString($data, 'complement'),
            'province' => self::optionalString($data, 'province')
                ?? self::optionalString($data, 'neighborhood'),
            'postalCode' => $data['postalCode'] ?? null,
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
     * Validates the sanitized data for the update operation.
     *
     * @internal
     *
     * @throws InvalidCustomerDataException
     */
    private static function validate(array $data): array
    {

        try {
            if (! ($data['cpfCnpj'] instanceof Cpf || $data['cpfCnpj'] instanceof Cnpj)) {
                if ($data['cpfCnpj'] !== null) {
                    $sanitized = DataSanitizer::onlyDigits($data['cpfCnpj']);
                    $length = strlen($sanitized ?? '');

                    $data['cpfCnpj'] = match ($length) {
                        11 => Cpf::from($data['cpfCnpj']),
                        14 => Cnpj::from($data['cpfCnpj']),
                        default => throw new InvalidValueObjectException('CPF or CNPJ must contain 11 or 14 digits'),
                    };
                }
            }

            self::validateSimpleValueObject($data, 'email', Email::class);
            self::validateSimpleValueObject($data, 'postalCode', PostalCode::class);
            self::validateSimpleValueObject($data, 'phone', Phone::class);
            self::validateSimpleValueObject($data, 'mobilePhone', Phone::class);
        } catch (InvalidValueObjectException $e) {
            throw new InvalidCustomerDataException($e->getMessage(), 0, $e);
        }

        return $data;
    }
}
