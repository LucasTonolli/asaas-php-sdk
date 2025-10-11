<?php

declare(strict_types=1);

namespace AsaasPhpSdk\Enums;

use AsaasPhpSdk\Helpers\DataSanitizer;

enum BillingType
{
    case Undefined;
    case Boleto;
    case CreditCard;
    case Pix;

    public function label(): string
    {
        return match ($this) {
            self::Boleto => 'Boleto',
            self::CreditCard => 'Cartão de Crédito',
            self::Pix => 'Pix',
            default => 'Desconhecido',
        };
    }

    private static function fromString(string $value): self
    {
        $normalized = DataSanitizer::sanitizeLowercase($value);

        return match ($normalized) {
            'boleto' || 'boleto_bancario' || 'ticket' => self::Boleto,
            'cartão de crédito' || 'credit_card' => self::CreditCard,
            'pix' => self::Pix,
            default => self::Undefined,
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
            self::Boleto,
            self::CreditCard,
            self::Pix,
            self::Undefined,
        ];
    }

    public static function options(): array
    {
        return array_map(fn ($case) => [$case => $case->label()], self::all());
    }
}
