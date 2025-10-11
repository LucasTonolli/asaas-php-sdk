<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;

enum DiscountType
{
    case Fixed;
    case Percentage;

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixo',
            self::Percentage => 'Porcentagem',
            default => 'Fixo',
        };
    }

    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        return match ($normalized) {
            'fixed' || 'fixo' => self::Fixed,
            'percentage' || 'porcentagem' => self::Percentage,
            default => self::Fixed,
        };
    }

    public static function tryFromString(string $value): ?self
    {
        try {
            return self::fromString($value);
        } catch (\ValueError) {
            return null;
        }
    }

    public static function all(): array
    {
        return [
            self::Fixed,
            self::Percentage,
        ];
    }

    public static function options(): array
    {
        return array_map(fn ($case) => [$case => $case->label()], self::all());
    }
}
