<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Helpers;

/**
 * A utility class for common data sanitization tasks.
 *
 * This class provides a collection of pure, static methods used throughout the SDK
 * to ensure data is in a clean and predictable format before validation. All
 * methods are designed to gracefully handle null inputs.
 *
 * @internal This is an internal helper class and is not intended for public use by SDK consumers.
 */
final class DataSanitizer
{
    /**
     * Removes all non-digit characters from a string.
     *
     * @param  ?string  $value  The string to sanitize.
     * @return ?string The sanitized string containing only digits, or null.
     */
    public static function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $value);

        return $cleaned === '' ? null : $cleaned;
    }

    /**
     * Trims whitespace from the beginning and end of a string.
     *
     * @param  ?string  $value  The string to sanitize.
     * @return ?string The trimmed string, or null if the result is empty.
     */
    public static function sanitizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Normalizes whitespace in a string, replacing multiple spaces with a single one.
     *
     * @param  ?string  $value  The string to normalize.
     * @return ?string The normalized string, or null if the result is empty.
     */
    public static function normalizeWhitespace(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/', ' ', trim($value));

        return $normalized === '' ? null : $normalized;
    }

    /**
     * Trims and converts a string to lowercase.
     *
     * @param  ?string  $value  The string to sanitize.
     * @return ?string The sanitized lowercase string, or null.
     */
    public static function sanitizeLowercase(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitized = self::sanitizeString($value);

        return $sanitized === null ? null : mb_strtolower($sanitized);
    }

    /**
     * Sanitizes a string intended to be an email address (trims and converts to lowercase).
     *
     * @param  ?string  $value  The email string to sanitize.
     * @return ?string The sanitized email string, or null.
     */
    public static function sanitizeEmail(?string $value): ?string
    {
        return self::sanitizeLowercase($value);
    }

    /**
     * Removes all non-alphanumeric characters from a string.
     *
     * @param  ?string  $value  The string to sanitize.
     * @return ?string The sanitized alphanumeric string, or null.
     */
    public static function onlyAlphaNumeric(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $value);

        return $cleaned === '' ? null : $cleaned;
    }

    /**
     * Sanitizes a mixed value into a boolean.
     *
     * It correctly interprets string values like 'true', '1', 'on', 'yes', 'sim'.
     *
     * @param  mixed  $value  The value to convert.
     * @return ?bool The sanitized boolean value, or null for empty inputs.
     */
    public static function sanitizeBoolean(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = mb_strtolower(trim($value));

            return in_array($lower, ['true', 'on', 'yes', 'y', '1', 'sim']);
        }

        return (bool) $value;
    }

    /**
     * Sanitizes a mixed value into an integer.
     *
     * @param  mixed  $value  The value to convert.
     * @return ?int The sanitized integer value, or null if not numeric.
     */
    public static function sanitizeInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    /**
     * Sanitizes a mixed value into a float.
     *
     * It correctly handles string numbers with both '.' and ',' as decimal separators.
     *
     * @param  mixed  $value  The value to convert.
     * @return ?float The sanitized float value, or null if not numeric.
     */
    public static function sanitizeFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $cleaned = str_replace(['.', ','], ['', '.'], $value);
            if (is_numeric($cleaned)) {
                return (float) $cleaned;
            }
        }

        return null;
    }
}
