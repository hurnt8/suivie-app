<?php

namespace Database\Seeders;

use App\Models\DestinationRate;
use Illuminate\Database\Seeder;

/**
 * Demo tariff grid: [destination, base amount, amount per kg], in the
 * platform currency. Idempotent — safe to run more than once.
 */
class DestinationRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['France', 6.90, 0.90],
            ['Paris', 5.90, 0.80],
            ['Belgique', 9.50, 1.20],
            ['Luxembourg', 9.50, 1.20],
            ['Suisse', 14.00, 1.80],
            ['Allemagne', 10.50, 1.30],
            ['Espagne', 11.00, 1.40],
            ['Italie', 11.00, 1.40],
            ['Portugal', 12.00, 1.50],
            ['Pologne', 12.50, 1.60],
            ['Roumanie', 13.50, 1.70],
            ['Royaume-Uni', 16.00, 2.10],
            ['Maroc', 19.00, 2.60],
            ['Canada', 28.00, 4.20],
            ['Etats-Unis', 32.00, 4.80],
            ['Chine', 36.00, 5.40],
            ['Japon', 38.00, 5.60],
        ];

        foreach ($rates as [$name, $base, $perKg]) {
            DestinationRate::query()->updateOrCreate(
                ['name' => $name],
                ['base_amount' => $base, 'per_kg_amount' => $perKg],
            );
        }
    }
}
