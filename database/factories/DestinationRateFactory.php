<?php

namespace Database\Factories;

use App\Models\DestinationRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DestinationRate>
 */
class DestinationRateFactory extends Factory
{
    protected $model = DestinationRate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'base_amount' => fake()->randomFloat(2, 5, 60),
            'per_kg_amount' => fake()->randomFloat(2, 0.5, 7),
        ];
    }
}
