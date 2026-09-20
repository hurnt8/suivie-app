<?php

namespace Database\Factories;

use App\Models\Sender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sender>
 */
class SenderFactory extends Factory
{
    protected $model = Sender::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company' => fake()->boolean(40) ? fake()->company() : null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
        ];
    }
}
