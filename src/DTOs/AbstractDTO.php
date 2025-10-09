<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs;

use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;
use AsaasPhpSdk\Exceptions\InvalidValueObjectException;
use AsaasPhpSdk\Helpers\DataSanitizer;

abstract class AbstractDTO implements DTOContract
{
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
                $method = $attributes[0]->newInstance()->method;
                $result[$key] = $value->{$method}();
            } elseif (is_object($value) && method_exists($value, 'value')) {
                $result[$key] = $value->value();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected static function validateValueObject(array &$data, string $key, string $valueObjectClass): void
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

    protected static function optionalString(array $data, string $key): ?string
    {
        return array_key_exists($key, $data)
            ? DataSanitizer::sanitizeString($data[$key])
            : null;
    }

    protected static function optionalBoolean(array $data, string $key): ?bool
    {
        return array_key_exists($key, $data)
            ? DataSanitizer::sanitizeBoolean($data[$key])
            : null;
    }

    protected static function optionalOnlyDigits(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }

        return DataSanitizer::onlyDigits($data[$key]);
    }

    protected static function optionalInteger(array $data, string $key): ?int
    {
        if (!array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
            return null;
        }
        return DataSanitizer::sanitizeInteger($data[$key]);
    }

    abstract protected static function sanitize(array $data): array;
}
