<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs;

/**
 *  Contract for Data Transfer Object
 * 
 * Defines the standard interface for a Data Transfer Object
 */
interface DTOContract
{
    /**
     * Convert the DTO to an array
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Create a DTO instance from an array
     * 
     * @param  array  $data array<string, mixed> $data
     * @return self
     */

    public static function fromArray(array $data): self;
}
