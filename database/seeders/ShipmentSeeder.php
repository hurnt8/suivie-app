<?php

namespace Database\Seeders;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\ShipmentStatus;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Shipment;
use App\Models\ShipmentNotification;
use App\Models\TrackingEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demo shipments spread across every status, each with a coherent event
 * history (a "delivered" shipment has the full chain of events; an
 * "in_transit" one stops midway). All data is fictional, for demo purposes.
 */
class ShipmentSeeder extends Seeder
{
    private const NOTIFIABLE = [
        ShipmentStatus::Registered,
        ShipmentStatus::PickedUp,
        ShipmentStatus::InTransit,
        ShipmentStatus::DestinationCenter,
        ShipmentStatus::OutForDelivery,
        ShipmentStatus::Delivered,
        ShipmentStatus::Delayed,
    ];

    public function run(): void
    {
        $senders = Sender::factory()->count(15)->create();
        $recipients = Recipient::factory()->count(15)->create();

        $statusPool = [
            ShipmentStatus::Pending,
            ShipmentStatus::Registered,
            ShipmentStatus::PickedUp,
            ShipmentStatus::SortingCenter,
            ShipmentStatus::InTransit,
            ShipmentStatus::InTransit,
            ShipmentStatus::Customs,
            ShipmentStatus::DestinationCenter,
            ShipmentStatus::OutForDelivery,
            ShipmentStatus::Delivered,
            ShipmentStatus::Delivered,
            ShipmentStatus::Delivered,
            ShipmentStatus::Delivered,
            ShipmentStatus::Delayed,
            ShipmentStatus::Returned,
            ShipmentStatus::Cancelled,
        ];

        for ($i = 0; $i < 40; $i++) {
            $status = fake()->randomElement($statusPool);

            $shipment = Shipment::factory()
                ->withStatus($status)
                ->create([
                    'sender_id' => $senders->random()->id,
                    'recipient_id' => $recipients->random()->id,
                ]);

            $this->buildHistory($shipment, $status);
        }
    }

    private function buildHistory(Shipment $shipment, ShipmentStatus $status): void
    {
        $steps = ShipmentStatus::timelineSteps();
        $index = array_search($status, $steps, true);
        $date = Carbon::instance($shipment->created_at);

        if ($index !== false) {
            foreach (array_slice($steps, 0, $index + 1) as $step) {
                $date = $date->copy()->addHours(random_int(4, 30));
                $this->createEvent($shipment, $step, $date);
            }

            return;
        }

        if ($status === ShipmentStatus::Pending) {
            return;
        }

        // Delayed / returned / cancelled: simulate partial progress then the exception.
        foreach (array_slice($steps, 0, random_int(2, 5)) as $step) {
            $date = $date->copy()->addHours(random_int(4, 30));
            $this->createEvent($shipment, $step, $date);
        }

        $date = $date->copy()->addHours(random_int(4, 30));
        $this->createEvent($shipment, $status, $date);
    }

    private function createEvent(Shipment $shipment, ShipmentStatus $status, Carbon $date): void
    {
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'status' => $status,
            'title' => $status->label(),
            'description' => $status->description(),
            'location' => fake()->city().', '.fake()->country(),
            'event_date' => $date,
            'created_by' => null,
        ]);

        if (in_array($status, self::NOTIFIABLE, true)) {
            ShipmentNotification::create([
                'shipment_id' => $shipment->id,
                'recipient' => $shipment->sender->email,
                'type' => NotificationChannel::Mail,
                'subject' => $status->label(),
                'status' => NotificationStatus::Sent,
                'sent_at' => $date->copy()->addMinutes(random_int(1, 5)),
            ]);
        }
    }
}
