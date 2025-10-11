<?php

namespace AsaasPhpSdk\ValueObjects\Contracts;

/**
 * Defines a contract for Value Objects that have a user-friendly formatted representation.
 *
 * This interface should be implemented by Value Objects that store a raw, sanitized
 * value but can also provide a "pretty-printed" version for display purposes.
 *
 * @example
 * $cpf = Cpf::from('12345678900');
 * echo $cpf->value();     // Outputs: '12345678900'
 * echo $cpf->formatted(); // Outputs: '123.456.789-00'
 */
interface FormattableContract
{
    public function formatted(): string;
}
