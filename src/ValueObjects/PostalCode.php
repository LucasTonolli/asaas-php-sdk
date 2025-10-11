<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractSimpleValueObject;
use AsaasPhpSdk\ValueObjects\Contracts\FormattableContract;

/**
 * A Value Object representing a Brazilian postal code (CEP).
 *
 * This class ensures that a postal code is always valid upon creation by
 * sanitizing the input and validating that it contains exactly 8 digits.
 * It stores the value internally as a digits-only string.
 */
class PostalCode extends AbstractSimpleValueObject implements FormattableContract
{
    /**
     * Creates a PostalCode instance from a string.
     *
     * This method sanitizes the input to keep only digits and validates that
     * the length is exactly 8 characters.
     *
     * @param  string  $postalCode  The postal code (CEP), which can be formatted or unformatted.
     * @return self A new, validated PostalCode instance.
     *
     * @throws InvalidPostalCodeException if the postal code is empty or has an invalid length.
     */
    public static function from(string $postalCode): self
    {
        $sanitized = DataSanitizer::onlyDigits($postalCode);

        if ($sanitized === null || strlen($sanitized) !== 8) {
            throw new \AsaasPhpSdk\Exceptions\InvalidPostalCodeException('Postal code must contain exactly 8 digits');
        }

        return new self($sanitized);
    }

    /**
     * Returns the postal code formatted as XXXXX-XXX.
     *
     * @return string The formatted postal code string.
     */
    public function formatted(): string
    {
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->value);
    }
}
