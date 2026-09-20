<?php

namespace App\Services;

use App\Models\DestinationRate;
use Illuminate\Support\Str;

/**
 * Prices a shipment from its destination and weight.
 *
 * A rate applies when its name appears as whole words inside the shipment's
 * free-text destination, ignoring case and accents ("France" matches
 * "Lyon, France"; "Etats-Unis" matches "New York, États-Unis"). When several
 * rates match, the most specific (longest) name wins.
 */
class QuoteService
{
    /**
     * @return array{amount: float, rate: DestinationRate}|null null when no rate covers the destination
     */
    public function quote(?string $destination, float|int|string|null $weight = null): ?array
    {
        $haystack = ' '.$this->normalize((string) $destination).' ';

        if (trim($haystack) === '') {
            return null;
        }

        $rate = DestinationRate::query()
            ->get()
            ->sortByDesc(fn (DestinationRate $rate): int => mb_strlen($rate->name))
            ->first(function (DestinationRate $rate) use ($haystack): bool {
                $needle = $this->normalize($rate->name);

                return $needle !== '' && str_contains($haystack, ' '.$needle.' ');
            });

        if ($rate === null) {
            return null;
        }

        $kilograms = is_numeric($weight) ? max(0.0, (float) $weight) : 0.0;

        return [
            'amount' => round((float) $rate->base_amount + (float) $rate->per_kg_amount * $kilograms, 2),
            'rate' => $rate,
        ];
    }

    private function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->trim()
            ->toString();
    }
}
