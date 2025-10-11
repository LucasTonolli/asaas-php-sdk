<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Base\AbstractSimpleValueObject;

/**
 * A Value Object representing a valid email address.
 *
 * This class ensures that an email address is always in a valid and normalized
 * format (lowercase, trimmed). It validates the format upon creation using
 * PHP's native email filter.
 */
class Email extends AbstractSimpleValueObject
{
    /**
     * Creates an Email instance from a string.
     *
     * This method sanitizes the email (trims whitespace, converts to lowercase)
     * and validates it against the `FILTER_VALIDATE_EMAIL` standard.
     *
     * @param  string  $email  The email address to validate and encapsulate.
     * @return self A new, validated Email instance.
     *
     * @throws InvalidEmailException if the email address is not in a valid format.
     */
    public static function from(string $email): self
    {
        $sanitized = DataSanitizer::sanitizeEmail($email);

        if (! filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw new \AsaasPhpSdk\Exceptions\InvalidEmailException('Email is not valid');
        }

        return new self($sanitized);
    }
}
