<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs;

use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\InvalidValueObjectException;
use AsaasPhpSdk\Helpers\DataSanitizer;

/**
 * Base class for all Data Transfer Objects (DTOs).
 *
 * Provides shared functionality for all DTOs, including a dynamic `toArray`
 * method and a suite of helper methods for sanitization and validation.
 * It also enforces that any concrete DTO must implement its own `sanitize` method.
 *
 * @internal This is an internal framework class and should not be used directly.
 */
abstract class AbstractDTO implements DTOContract
{
    /**
     * Converts the DTO's public properties to an associative array.
     *
     * This method uses Reflection to dynamically build an array. It intelligently
     * handles Value Objects by checking for a `#[ToArrayMethodAttribute]` to call a
     * custom method (e.g., `->formatted()`); otherwise, it defaults to `->value()`.
     * Properties with `null` values are excluded from the output.
     *
     * @return array<string, mixed> The DTO's data as an array.
     */
    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $result = [];

        foreach ($properties as $property) {
            $key = $property->getName();
            $value = $property->getValue($this);

            if ($value === null) {
                continue;
            }

            $attributes = $property->getAttributes(ToArrayMethodAttribute::class);
            if (! empty($attributes)) {
                $attr = $attributes[0]->newInstance();
                $method = $attr->method;
                $args = $attr->args ?? [];
                $result[$key] = $value->{$method}(...$args);
            } elseif (is_object($value) && method_exists($value, 'value')) {
                $result[$key] = $value->value();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Validates and instantiates a Value Object from a raw data key.
     *
     * Attempts to create a VO using its `from()` static constructor. If creation
     * fails, it wraps the original exception in an `InvalidValueObjectException`.
     *
     * @param  array<string, mixed>  &$data  The data array, passed by reference.
     * @param  string  $key  The key in the data array to validate.
     * @param  class-string  $valueObjectClass  The fully qualified class name of the Value Object.
     *
     * @throws InvalidValueObjectException if the value is invalid and the VO cannot be created.
     */
    protected static function validateSimpleValueObject(array &$data, string $key, string $valueObjectClass): void
    {
        if (! isset($data[$key]) || $data[$key] === null) {
            return;
        }

        try {
            $data[$key] = $valueObjectClass::from($data[$key]);
        } catch (\Exception $e) {
            throw new InvalidValueObjectException(
                "Invalid format for '{$key}': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    protected static function validateStructuredValueObject(array &$data, string $key, string $voClass): void
    {
        if (isset($data[$key]) && is_array($data[$key])) {
            try {
                $data[$key] = $voClass::fromArray($data[$key]);
            } catch (\Exception $e) {
                throw new InvalidValueObjectException(
                    "Invalid format for '{$key}': " . $e->getMessage(),
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Sanitizes an optional string value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?string The sanitized string, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalString(array $data, string $key): ?string
    {
        return array_key_exists($key, $data)
            ? DataSanitizer::sanitizeString($data[$key])
            : null;
    }

    /**
     * Sanitizes an optional boolean value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?bool The sanitized boolean, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalBoolean(array $data, string $key): ?bool
    {
        return array_key_exists($key, $data)
            ? DataSanitizer::sanitizeBoolean($data[$key])
            : null;
    }

    /**
     * Sanitizes an optional string value from the data array, keeping only digits.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?string The sanitized string of digits, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalOnlyDigits(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::onlyDigits($data[$key]);
    }

    /**
     * Sanitizes an optional integer value from the data array.
     *
     * @param  array<string, mixed>  $data  The source data array.
     * @param  string  $key  The key to look for.
     * @return ?int The sanitized integer, or null if the key doesn't exist or the value is empty.
     */
    protected static function optionalInteger(array $data, string $key): ?int
    {
        if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::sanitizeInteger($data[$key]);
    }

    protected static function optionalFloat(array $data, string $key): ?float
    {
        if (! array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::sanitizeFloat($data[$key]);
    }
    /**
     * Defines the contract for sanitizing the DTO's raw input data.
     *
     * Each concrete DTO class must implement this method to clean and normalize
     * its specific set of properties before any validation occurs.
     *
     * @param  array<string, mixed>  $data  The raw input data.
     * @return array<string, mixed> The sanitized data array.
     */
    abstract protected static function sanitize(array $data): array;
}
