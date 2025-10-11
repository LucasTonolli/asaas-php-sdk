<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractSimpleValueObject;
use AsaasPhpSdk\ValueObjects\Contracts\FormattableContract;

/**
 * A Value Object representing a Brazilian Corporate Taxpayer Registry (CNPJ).
 *
 * This class ensures that a CNPJ is always valid upon creation by sanitizing
 * the input and validating its length and checksum digits according to the
 * official algorithm. It internally stores the CNPJ as a digits-only string.
 */
class Cnpj extends AbstractSimpleValueObject implements FormattableContract
{
    /**
     * Creates a Cnpj instance from a string.
     *
     * This method sanitizes the input to keep only digits and then validates
     * its format and checksum.
     *
     * @param  string  $cnpj  The CNPJ number, which can be formatted or unformatted.
     * @return self A new, validated Cnpj instance.
     *
     * @throws InvalidCnpjException if the CNPJ is invalid.
     */
    public static function from(string $cnpj): self
    {
        $sanitized = DataSanitizer::onlyDigits($cnpj);

        if ($sanitized === null || strlen($sanitized) !== 14) {
            throw new \AsaasPhpSdk\Exceptions\InvalidCnpjException('Cnpj must contain exactly 14 digits');
        }

        if (! self::isValidCnpj($sanitized)) {
            throw new \AsaasPhpSdk\Exceptions\InvalidCnpjException("Invalid Cnpj: {$cnpj}");
        }

        return new self($sanitized);
    }

    /**
     * Validates a CNPJ number based on the official Brazilian algorithm.
     *
     * This static helper method checks if a given string is a mathematically
     * valid CNPJ, including the checksum digits. It automatically handles and
     * ignores non-digit characters.
     *
     * @param  string  $cnpj  The CNPJ string to validate.
     * @return bool True if the CNPJ is valid, false otherwise.
     */
    public static function isValidCnpj(string $cnpj): bool
    {
        $cnpj = DataSanitizer::onlyDigits($cnpj) ?? '';

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ((int) $cnpj[$i]) * $weights[$i];
        }
        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        if ($digit1 !== (int) $cnpj[12]) {
            return false;
        }

        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += ((int) $cnpj[$i]) * $weights[$i];
        }
        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        return $digit2 === (int) $cnpj[13];
    }

    /**
     * Returns the CNPJ formatted as XX.XXX.XXX/XXXX-XX.
     *
     * @return string The formatted CNPJ string.
     */
    public function formatted(): string
    {
        return preg_replace(
            '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
            '$1.$2.$3/$4-$5',
            $this->value
        );
    }
}
