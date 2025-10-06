<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs\Customer;

use AsaasPhpSdk\Exceptions\InvalidCustomerDataException;
use AsaasPhpSdk\Helper\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;
use AsaasPhpSdk\ValueObjects\Phone;
use AsaasPhpSdk\ValueObjects\PostalCode;
use Symfony\Component\VarDumper\Cloner\Data;

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
            name: $validatedData['name'],
            cpfCnpj: $validatedData['cpfCnpj'],
            email: $validatedData['email'],
            phone: $validatedData['phone'],
            mobilePhone: $validatedData['mobilePhone'],
            address: $validatedData['address'],
            addressNumber: $validatedData['addressNumber'],
            complement: $validatedData['complement'],
            province: $validatedData['province'],
            postalCode: $validatedData['postalCode'],
            externalReference: $validatedData['externalReference'],
            notificationDisabled: $validatedData['notificationDisabled'],
            additionalEmails: $validatedData['additionalEmails'],
            municipalInscription: $validatedData['municipalInscription'],
            stateInscription: $validatedData['stateInscription'],
            observations: $validatedData['observations'],
            groupName: $validatedData['groupName'],
            company: $validatedData['company'],
            foreignCustomer: $validatedData['foreignCustomer']
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

        return array_filter($data, fn($value) => $value !== null && $value !== false);
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
            $documentLength = strlen(DataSanitizer::onlyNumbers($data['cpfCnpj']));
            if ($documentLength !== 11 && $documentLength !== 14) {
                throw InvalidCustomerDataException::invalidFormat('cpfCnpj', 'CPF or CNPJ must contain 11 or 14 digits');
            }
            $data['cpfCnpj'] = $documentLength === 11 ? Cpf::from($data['cpfCnpj']) : Cnpj::from($data['cpfCnpj']);
        } catch (\Exception $e) {
            throw InvalidCustomerDataException::invalidFormat('cpfCnpj', $e->getMessage());
        }

        try {
            if ($data['email']) {
                $data['email'] = Email::from($data['email']);
            }
        } catch (\Exception $e) {
            throw InvalidCustomerDataException::invalidFormat('email', $e->getMessage());
        }

        try {
            if ($data['postalCode']) {
                $data['postalCode'] = PostalCode::from($data['postalCode']);
            }
        } catch (\Exception $e) {
            throw InvalidCustomerDataException::invalidFormat('postalCode', $e->getMessage());
        }

        try {
            if ($data['phone']) {
                $data['phone'] = Phone::from($data['phone']);
            }
        } catch (\Exception $e) {
            throw InvalidCustomerDataException::invalidFormat('phone', $e->getMessage());
        }

        try {
            if ($data['mobilePhone']) {
                $data['mobilePhone'] = Phone::from($data['mobilePhone']);
            }
        } catch (\Exception $e) {
            throw InvalidCustomerDataException::invalidFormat('mobilePhone', $e->getMessage());
        }

        return $data;
    }
}
