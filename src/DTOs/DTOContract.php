<?php

declare(strict_types=1);

namespace AsaasPhpSdk\DTOs;

/**
 * Defines the essential public API for all Data Transfer Objects (DTOs).
 *
 * This contract ensures that every DTO in the SDK is both serializable into an
 * array and constructible from a raw data array, providing a consistent
 * interface for data handling across the application.
 */
interface DTOContract
{
    /**
     * Converts the DTO instance into an associative array.
     *
     * This method is used for serializing the DTO's validated data, making it
     * suitable for API request bodies, logging, or storage.
     *
     * @return array<string, mixed> The DTO's data as an associative array.
     */
    public function toArray(): array;

    /**
     * Creates a DTO instance from a raw associative array.
     *
     * This static factory method is the primary entry point for creating a DTO.
     * It is responsible for taking raw input and initiating the object's
     * sanitization and validation process.
     *
     * @param  array<string, mixed>  $data  The raw data array.
     * @return static A new instance of the implementing DTO class.
     */
    public static function fromArray(array $data): self;
}
