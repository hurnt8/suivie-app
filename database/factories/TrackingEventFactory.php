<?php

namespace Database\Factories;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrackingEvent>
 */
class TrackingEventFactory extends Factory
{
    protected $model = TrackingEvent::class;

    public function definition(): array
    {
        $status = fake()->randomElement(ShipmentStatus::timelineSteps());

        return [
            'shipment_id' => Shipment::factory(),
            'status' => $status,
            'title' => $status->label(),
            'description' => $status->description(),
            'location' => fake()->city().', '.fake()->country(),
            'event_date' => fake()->dateTimeBetween('-2 months', 'now'),
            'created_by' => null,
        ];
    }
}
