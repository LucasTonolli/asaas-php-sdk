<?php

namespace AsaasPhpSdk\DTOs\Customers;

use AsaasPhpSdk\DTOs\AbstractDTO;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;

class ListCustomersDTO extends AbstractDTO
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

    protected static function sanitize(array $data): array
    {
        return [
            'offset' => self::optionalInteger($data, 'offset'),
            'limit' => self::optionalInteger($data, 'limit'),
            'name' => self::optionalString($data, 'name'),
            'email' => self::optionalEmail($data['email'] ?? null),
            'cpfCnpj' => self::optionalCpfCnpj($data['cpfCnpj'] ?? null),
            'groupName' => self::optionalString($data, 'groupName'),
            'externalReference' => self::optionalString($data, 'externalReference'),
        ];
    }

    private static function optionalEmail(?string $email): ?Email
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

    private static function optionalCpfCnpj(?string $cpfCnpj): Cpf|Cnpj|null
    {
        if ($cpfCnpj === null) {
            return null;
        }

        $sanitized = DataSanitizer::onlyDigits($cpfCnpj);
        if ($sanitized === null) {
            return null;
        }

        $length = strlen($sanitized);

        try {
            return match ($length) {
                11 => Cpf::from($sanitized),
                14 => Cnpj::from($sanitized),
                default => null
            };
        } catch (\Exception) {
            return null;
        }
    }
}
