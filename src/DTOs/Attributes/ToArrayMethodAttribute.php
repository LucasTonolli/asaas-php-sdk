<?php

namespace AsaasPhpSdk\DTOs\Attributes;

use Attribute;

/**
 * A PHP Attribute to specify a custom method for array conversion in DTOs.
 *
 * When a DTO is converted to an array, its Value Object properties are usually
 * serialized by calling their `->value()` method. This attribute allows you to
 * override that default behavior for a specific property.
 *
 * It is especially useful for Value Objects that have multiple string representations,
 * such as a raw value versus a formatted value.
 *
 * @example
 * final class CreatePaymentDTO extends AbstractDTO
 * {
 * // When this DTO's toArray() is called, it will use $this->dueDate->formatted()
 * // instead of the default $this->dueDate->value().
 * #[ToArrayMethodAttribute('formatted')]
 * public readonly ?Date $dueDate = null;
 * }
 */
#[Attribute(\Attribute::TARGET_PROPERTY)]
final class ToArrayMethodAttribute
{
    public function __construct(public readonly string $method, public readonly array $args = []) {}
}
