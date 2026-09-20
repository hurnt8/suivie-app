<?php

use App\Enums\ShipmentStatus;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\Shipment\ShipmentStatusNotification;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Notification;

test('creating a shipment generates a tracking code, an initial event and a notification', function () {
    Notification::fake();

    $data = [
        'sender' => Sender::factory()->make()->toArray(),
        'recipient' => Recipient::factory()->make()->toArray(),
        'shipment' => [
            'description' => 'Colis test',
            'weight' => 2.5,
            'package_count' => 1,
            'shipment_type' => 'parcel',
            'service_type' => 'standard',
            'origin' => 'Paris, France',
            'destination' => 'Lyon, France',
            'estimated_delivery_date' => now()->addDays(5)->toDateString(),
        ],
    ];

    $shipment = app(ShipmentService::class)->create($data);

    expect($shipment->tracking_code)->toMatch('/^[A-Z]+-\d{4}-[A-Z0-9]{6}$/')
        ->and($shipment->current_status)->toBe(ShipmentStatus::Registered)
        ->and($shipment->events()->count())->toBe(1);

    Notification::assertSentOnDemand(ShipmentStatusNotification::class);
});

test('creating a shipment can reuse an existing sender and recipient', function () {
    Notification::fake();

    $sender = Sender::factory()->create();
    $recipient = Recipient::factory()->create();

    $shipment = app(ShipmentService::class)->create([
        'sender' => ['id' => $sender->id],
        'recipient' => ['id' => $recipient->id],
        'shipment' => [
            'package_count' => 1,
            'shipment_type' => 'parcel',
            'service_type' => 'standard',
            'origin' => 'Paris, France',
            'destination' => 'Berlin, Germany',
        ],
    ]);

    expect(Sender::count())->toBe(1)
        ->and(Recipient::count())->toBe(1)
        ->and($shipment->sender_id)->toBe($sender->id)
        ->and($shipment->recipient_id)->toBe($recipient->id);
});

test('changing status creates a new event and updates current_status', function () {
    Notification::fake();

    $shipment = Shipment::factory()->create(['current_status' => ShipmentStatus::Registered]);
    $actor = User::factory()->admin()->create();

    app(ShipmentService::class)->changeStatus($shipment, ShipmentStatus::InTransit, [
        'location' => 'Marseille, France',
    ], $actor);

    $shipment->refresh();

    expect($shipment->current_status)->toBe(ShipmentStatus::InTransit)
        ->and($shipment->events()->latest('event_date')->first()->created_by)->toBe($actor->id);
});

test('marking a shipment delivered stamps delivered_at', function () {
    Notification::fake();

    $shipment = Shipment::factory()->create(['current_status' => ShipmentStatus::OutForDelivery, 'delivered_at' => null]);

    app(ShipmentService::class)->changeStatus($shipment, ShipmentStatus::Delivered);

    expect($shipment->fresh()->delivered_at)->not->toBeNull();
});
