<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Exceptions\InvalidPhoneException;
use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractSimpleValueObject;
use AsaasPhpSdk\ValueObjects\Contracts\FormattableContract;

/**
 * A Value Object representing a Brazilian phone number.
 *
 * This class validates that a phone number has either 10 (landline) or 11 (mobile)
 * digits after sanitization. It stores the value internally as a digits-only
 * string and provides methods for formatting and type checking.
 */
final class Phone extends AbstractSimpleValueObject implements FormattableContract
{
    /**
     * Creates a Phone instance from a string.
     *
     * This method sanitizes the input to keep only digits and validates that
     * the length is either 10 or 11 characters.
     *
     * @param  string  $phone  The phone number, which can be formatted or unformatted.
     * @return self A new, validated Phone instance.
     *
     * @throws InvalidPhoneException if the phone number is empty or has an invalid length.
     */
    public static function from(string $phone): self
    {
        $sanitized = DataSanitizer::onlyDigits($phone);

        if ($sanitized === null) {
            throw new InvalidPhoneException('Phone number cannot be empty');
        }

        $length = strlen($sanitized);
        if ($length !== 10 && $length !== 11) {
            throw new InvalidPhoneException(
                'Phone must contain 10 or 11 digits'
            );
        }

        return new self($sanitized);
    }

    /**
     * Returns the phone number in a standard Brazilian format.
     *
     * It formats mobile numbers (11 digits) as `(XX) XXXXX-XXXX` and
     * landlines (10 digits) as `(XX) XXXX-XXXX`.
     *
     * @return string The formatted phone number string.
     */
    public function formatted(): string
    {
        if (strlen($this->value) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->value);
        }

        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $this->value);
    }

    /**
     * Checks if the phone number is a mobile number.
     *
     * A number is considered mobile if it contains 11 digits.
     *
     * @return bool True if it is a mobile number, false otherwise.
     */
    public function isMobile(): bool
    {
        return strlen($this->value) === 11;
    }

    /**
     * Checks if the phone number is a landline number.
     *
     * A number is considered a landline if it contains 10 digits.
     *
     * @return bool True if it is a landline number, false otherwise.
     */
    public function isLandline(): bool
    {
        return strlen($this->value) === 10;
    }
}
