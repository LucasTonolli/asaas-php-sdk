<?php

namespace AsaasPhpSdk\ValueObjects\Traits;

use AsaasPhpSdk\ValueObjects\ValueObjectContract;

trait StringValueObject
{
    private readonly string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectContract $other): bool
    {
        return $other instanceof static && $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
