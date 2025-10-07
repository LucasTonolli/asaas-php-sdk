<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Helpers;

/**
 * Helper class for common data sanitization
 */
final class DataSanitizer
{
    public static function onlyDigits(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $value);

        return $cleaned === '' ? null : $cleaned;
    }

    public static function sanitizeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    public static function normalizeWhitespace(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/', ' ', $value);

        return $normalized === '' ? null : $normalized;
    }

    public static function sanitizeLowercase(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitized = self::sanitizeString($value);

        return $sanitized === null ? null : mb_strtolower($sanitized);
    }

    public static function sanitizeEmail(?string $value): ?string
    {
        return self::sanitizeLowercase($value);
    }

    public static function onlyAlphaNumeric(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $value);

        return $cleaned === '' ? null : $cleaned;
    }

    public static function sanitizeBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = mb_strtolower(trim($value));

            return in_array($lower, ['true', 'on', 'yes', 'y', '1', 'sim']);
        }

        return (bool) $value;
    }

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
