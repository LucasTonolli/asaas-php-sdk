<?php

namespace AsaasPhpSdk\ValueObjects;

use AsaasPhpSdk\Helpers\DataSanitizer;
use AsaasPhpSdk\ValueObjects\Traits\StringValueObject;

class Email implements ValueObjectContract
{
    use StringValueObject;

    public static function from(string $email): self
    {
        $sanitized = DataSanitizer::sanitizeEmail($email);

        if (! filter_var($sanitized, FILTER_VALIDATE_EMAIL)) {
            throw new \AsaasPhpSdk\Exceptions\InvalidEmailException('Email is not valid');
        }

        return new self($sanitized);
    }
}
