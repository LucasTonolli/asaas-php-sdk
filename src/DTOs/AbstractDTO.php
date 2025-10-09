<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs;

use AsaasPhpSdk\DTOs\Attributes\ToArrayMethodAttribute;

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
                $method = $attributes[0]->newInstance()->methodName;
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
            throw new \InvalidArgumentException(
                "Invalid format for '{$key}': ".$e->getMessage(),
                0,
                $e
            );
        }
    }

    abstract protected static function sanitize(array $data): array;
}
