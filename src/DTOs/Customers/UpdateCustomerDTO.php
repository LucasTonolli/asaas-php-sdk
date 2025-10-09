<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customers;

use AsaasPhpSdk\DTOs\AbstractDTO;
use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\InvalidCnpjException;
use AsaasPhpSdk\Exceptions\InvalidCpfException;
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
        } catch (InvalidCnpjException $e) {
            throw new InvalidCustomerDataException($e->getMessage(), 0, $e);
        } catch (InvalidCpfException $e) {
            throw new InvalidCustomerDataException($e->getMessage(), 0, $e);
        }

        return $data;
    }
}
