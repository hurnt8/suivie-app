<?php

namespace App\Services;

use App\Models\Settings;
use App\Models\Shipment;
use RuntimeException;

/**
 * Generates unique, hard-to-guess tracking codes such as `LVR-2026-8X92K7`.
 * Codes are never derived from the shipments' auto-incrementing id, so a
 * visitor can never enumerate shipments by guessing sequential numbers.
 */
class TrackingCodeService
{
    /**
     * Crockford base32 alphabet: uppercase letters and digits with the
     * visually ambiguous characters (0, O, 1, I, L) removed.
     */
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    private const RANDOM_LENGTH = 6;

    private const MAX_ATTEMPTS = 20;

    public function generate(?string $prefix = null): string
    {
        $prefix = $prefix ?? $this->defaultPrefix();

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $candidate = sprintf('%s-%s-%s', $prefix, now()->year, $this->randomSegment());

            if (! Shipment::where('tracking_code', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new RuntimeException('Unable to generate a unique tracking code after '.self::MAX_ATTEMPTS.' attempts.');
    }

    protected function defaultPrefix(): string
    {
        return Settings::current()->tracking_prefix ?: config('tracking.default_prefix', 'LVR');
    }

    protected function randomSegment(): string
    {
        $segment = '';

        for ($i = 0; $i < self::RANDOM_LENGTH; $i++) {
            $segment .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return $segment;
    }
}
