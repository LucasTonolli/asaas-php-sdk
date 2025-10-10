<?php

namespace AsaasPhpSdk\ValueObjects;

/**
 * Defines the essential contract for all Value Objects (VOs) in the SDK.
 *
 * This interface ensures that every VO encapsulates a primitive value, can be
 * compared based on its value (not identity), and can be represented as a string.
 *
 * By extending `\JsonSerializable` and `\Stringable`, it guarantees that all VOs
 * can be safely used in JSON and string contexts.
 */
interface ValueObjectContract extends \JsonSerializable, \Stringable
{
    /**
     * Gets the raw, primitive value of the object.
     *
     * @return string The underlying value.
     */
    public function value(): string;

    /**
     * Performs a value-based equality comparison.
     *
     * Checks if this Value Object is equal to another by comparing their
     * underlying values, not their object identities.
     *
     * @param  self  $other  The other Value Object to compare against.
     * @return bool True if the values are equal, false otherwise.
     */
    public function equals(self $other): bool;
}
