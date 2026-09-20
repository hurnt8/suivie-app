<?php

namespace App\Concerns;

/**
 * Shared field rules for Sender and Recipient, which have an identical
 * shape. `$prefix` lets the same rule set validate `sender.*` and
 * `recipient.*` Livewire-bound properties without duplicating the array.
 */
trait PartyValidationRules
{
    /**
     * @return array<string, mixed>
     */
    protected function partyRules(string $prefix): array
    {
        return [
            "{$prefix}.name" => ['required', 'string', 'max:255'],
            "{$prefix}.company" => ['nullable', 'string', 'max:255'],
            "{$prefix}.email" => ['required', 'email', 'max:255'],
            "{$prefix}.phone" => ['nullable', 'string', 'max:30'],
            "{$prefix}.address" => ['required', 'string', 'max:255'],
            "{$prefix}.city" => ['required', 'string', 'max:120'],
            "{$prefix}.postal_code" => ['required', 'string', 'max:20'],
            "{$prefix}.country" => ['required', 'string', 'max:120'],
        ];
    }
}
