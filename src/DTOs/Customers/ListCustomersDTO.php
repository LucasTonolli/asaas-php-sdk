<?php

namespace AsaasPhpSdk\DTOs\Customers;

use AsaasPhpSdk\DTOs\AbstractDTO;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Cnpj;
use AsaasPhpSdk\ValueObjects\Cpf;
use AsaasPhpSdk\ValueObjects\Email;

/**
 * A "Lenient" Data Transfer Object for filtering and paginating customers.
 *
 * This DTO is designed for flexibility. It sanitizes input data but does not
 * throw exceptions for invalid filter values. Instead, invalid or malformed
 * filters are silently ignored (converted to null), allowing for a robust
 * search experience without generating errors.
 */
class ListCustomersDTO extends AbstractDTO
{
    /**
     * ListCustomersDTO private constructor.
     *
     * @param  ?int  $offset  The starting position of the list for pagination.
     * @param  ?int  $limit  The maximum number of results to return per page.
     * @param  ?string  $name  Filter by customer's full name.
     * @param  ?Email  $email  Filter by customer's primary email.
     * @param  Cpf|Cnpj|null  $cpfCnpj  Filter by customer's document (CPF or CNPJ).
     * @param  ?string  $groupName  Filter by the name of the customer's group.
     * @param  ?string  $externalReference  Filter by the external unique identifier.
     */
    private function __construct(
        public readonly ?int $offset = null,
        public readonly ?int $limit = null,
        public readonly ?string $name = null,
        public readonly ?Email $email = null,
        public readonly Cpf|Cnpj|null $cpfCnpj = null,
        public readonly ?string $groupName = null,
        public readonly ?string $externalReference = null
    ) {}

    /**
     * Creates a new ListCustomersDTO instance from a raw array of filters.
     *
     * This factory method takes a raw array and sanitizes it. It does not
     * perform strict validation and will not throw exceptions for invalid filters.
     *
     * @param  array<string, mixed>  $data  Raw filter data.
     * @return self A new instance of the DTO with sanitized filters.
     */
    public static function fromArray(array $data): self
    {
        $sanitizedData = self::sanitize($data);

        return new self(...$sanitizedData);
    }

    /**
     * Sanitizes the raw filter data.
     *
     * @internal
     *
     * @param  array<string, mixed>  $data  The raw filter data.
     * @return array<string, mixed> The sanitized filter array.
     */
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

    /**
     * Safely attempts to create an Email Value Object. Returns null on failure.
     *
     * @internal
     */
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

    /**
     * Safely attempts to create a Cpf or Cnpj Value Object. Returns null on failure.
     *
     * @internal
     */
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
