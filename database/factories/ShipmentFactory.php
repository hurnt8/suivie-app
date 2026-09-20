<?php

namespace Database\Factories;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Enums\ShipmentType;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-3 months', 'now');

        return [
            'tracking_code' => 'LVR-'.now()->year.'-'.Str::upper(Str::random(6)),
            'sender_id' => Sender::factory(),
            'recipient_id' => Recipient::factory(),
            'description' => fake()->randomElement(['Documents commerciaux', 'Pièces détachées', 'Vêtements', 'Matériel électronique', 'Échantillons', 'Produits cosmétiques']),
            'weight' => fake()->randomFloat(2, 0.2, 45),
            'package_count' => fake()->numberBetween(1, 5),
            'shipment_type' => fake()->randomElement(ShipmentType::cases()),
            'service_type' => fake()->randomElement(ServiceType::cases()),
            'origin' => fake()->city().', '.fake()->country(),
            'destination' => fake()->city().', '.fake()->country(),
            'current_status' => ShipmentStatus::Pending,
            'estimated_delivery_date' => fake()->dateTimeBetween('now', '+10 days'),
            'special_instructions' => fake()->boolean(25) ? fake()->sentence() : null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function withStatus(ShipmentStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => $status,
            'shipped_at' => in_array($status, [ShipmentStatus::Pending, ShipmentStatus::Registered], true) ? null : now()->subDays(3),
            'delivered_at' => $status === ShipmentStatus::Delivered ? now()->subHours(fake()->numberBetween(1, 48)) : null,
        ]);
    }
}
