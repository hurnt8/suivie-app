<?php

namespace App\Concerns;

/**
 * Partial masking of personally identifiable information, used exclusively
 * on the public tracking page (§3/§13 of the spec) — never in the admin panel.
 */
trait Maskable
{
    public function maskedName(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === [] || $parts === false) {
            return '';
        }

        $first = array_shift($parts);
        $lastInitial = $parts !== [] ? mb_substr($parts[0], 0, 1).str_repeat('*', max(3, mb_strlen($parts[0]) - 1)) : '';

        return trim($first.' '.$lastInitial);
    }

    public function maskedEmail(): string
    {
        if (! str_contains((string) $this->email, '@')) {
            return '';
        }

        [$local, $domain] = explode('@', $this->email, 2);

        $visible = mb_substr($local, 0, 1);

        return $visible.str_repeat('*', max(3, mb_strlen($local) - 1)).'@'.$domain;
    }

    public function maskedPhone(): ?string
    {
        if (blank($this->phone)) {
            return null;
        }

        $digits = preg_replace('/\s+/', '', (string) $this->phone);
        $visible = mb_substr($digits, -2);

        return str_repeat('*', max(3, mb_strlen($digits) - 2)).$visible;
    }

    public function maskedCity(): string
    {
        return (string) $this->city;
    }
}
