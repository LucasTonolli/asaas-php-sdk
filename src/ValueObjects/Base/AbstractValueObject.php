<?php

namespace AsaasPhpSdk\ValueObjects\Base;

abstract class AbstractValueObject
{
    /**
     * Compares this Value Object with another for value equality.
     *
     * @param  self  $other  The other Value Object to compare with.
     * @return bool True if the objects are of the same type and their values are identical.
     */
    public function equals(self $other): bool
    {
        return $other instanceof static && $this === $other;
    }
}
