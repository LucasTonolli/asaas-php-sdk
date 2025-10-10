<?php

namespace AsaasPhpSdk\ValueObjects\Traits;

/**
 * Provides a standard implementation for string-based Value Objects.
 *
 * This trait includes the boilerplate code for a VO that encapsulates a single
 * string value. It provides a private constructor to enforce immutability, a
 * `value()` getter, equality comparison, and serialization methods.
 *
 * A class using this trait should provide its own static `from()` factory
 * method for construction and validation.
 *
 * @property-read string $value The raw, underlying string value.
 */
trait StringValueObject
{
    private readonly string $value;

    /**
     * Private constructor to enforce immutability and the factory pattern.
     *
     * @internal Should only be called from a static factory method like `from()`.
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Gets the raw, underlying string value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compares this Value Object with another for value equality.
     *
     * @param  self  $other  The other Value Object to compare with.
     * @return bool True if the objects are of the same type and their values are identical.
     */
    public function equals(self $other): bool
    {
        return $other instanceof static && $this->value === $other->value;
    }

    /**
     * Specifies the data which should be serialized to JSON.
     *
     * @see \JsonSerializable
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * Returns the string representation of the object.
     *
     * @see \Stringable
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
