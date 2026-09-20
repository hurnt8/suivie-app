<?php

namespace App\Support;

/**
 * The languages the platform ships translations for, with their native names.
 * Single source of truth for the language pickers (public switcher, settings,
 * shipment email language) and for validating a chosen locale.
 */
final class Locales
{
    public const NAMES = [
        'fr' => 'Français',
        'en' => 'English',
        'es' => 'Español',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'ro' => 'Română',
        'pl' => 'Polski',
    ];

    /**
     * @return array<int, string>
     */
    public static function codes(): array
    {
        return array_keys(self::NAMES);
    }

    public static function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, self::NAMES);
    }

    public static function name(?string $locale): ?string
    {
        return self::NAMES[$locale] ?? null;
    }
}
