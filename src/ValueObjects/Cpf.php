<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractSimpleValueObject;
use AsaasPhpSdk\ValueObjects\Contracts\FormattableContract;


/**
 * A Value Object representing a Brazilian Individual Taxpayer Registry (CPF).
 *
 * This class ensures that a CPF is always valid upon creation by sanitizing
 * the input and validating its length and checksum digits according to the
 * official algorithm. It internally stores the CPF as a digits-only string.
 */
class Cpf extends AbstractSimpleValueObject implements FormattableContract
{
    /**
     * Creates a Cpf instance from a string.
     *
     * This method sanitizes the input to keep only digits and then validates
     * its format and checksum.
     *
     * @param  string  $cpf  The CPF number, which can be formatted or unformatted.
     * @return self A new, validated Cpf instance.
     *
     * @throws InvalidCpfException if the CPF is invalid.
     */
    public static function from(string $cpf): self
    {
        $sanitized = DataSanitizer::onlyDigits($cpf);

        if ($sanitized === null || strlen($sanitized) !== 11) {
            throw new \AsaasPhpSdk\Exceptions\InvalidCpfException('CPF must contain exactly 11 digits');
        }

        if (! self::isValidCpf($sanitized)) {
            throw new \AsaasPhpSdk\Exceptions\InvalidCpfException("Invalid CPF: {$cpf}");
        }

        return new self($sanitized);
    }

    /**
     * Validates a CPF number based on the official Brazilian algorithm.
     *
     * This static helper method checks if a given string is a mathematically
     * valid CPF, including the checksum digits. It automatically handles and
     * ignores non-digit characters.
     *
     * @param  string  $cpf  The CPF string to validate.
     * @return bool True if the CPF is valid, false otherwise.
     */
    public static function isValidCpf(string $cpf): bool
    {
        $cpf = DataSanitizer::onlyDigits($cpf) ?? '';

        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $cpf[$i]) * (10 - $i);
        }
        $digit1 = 11 - ($sum % 11);
        $digit1 = $digit1 >= 10 ? 0 : $digit1;

        if ($digit1 !== (int) $cpf[9]) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += ((int) $cpf[$i]) * (11 - $i);
        }
        $digit2 = 11 - ($sum % 11);
        $digit2 = $digit2 >= 10 ? 0 : $digit2;

        return $digit2 === (int) $cpf[10];
    }

    /**
     * Returns the CPF formatted as XXX.XXX.XXX-XX.
     *
     * @return string The formatted CPF string.
     */
    public function formatted(): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->value);
    }
}
