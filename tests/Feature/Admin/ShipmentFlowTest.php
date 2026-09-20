<?php

use App\Enums\ShipmentStatus;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\Shipment\ShipmentStatusNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('an admin can create a shipment end to end and the sender is emailed', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $sender = Sender::factory()->make();
    $recipient = Recipient::factory()->make();

    Livewire::test('pages::admin.shipments.create')
        ->set('sender.name', $sender->name)
        ->set('sender.email', $sender->email)
        ->set('sender.address', $sender->address)
        ->set('sender.city', $sender->city)
        ->set('sender.postal_code', $sender->postal_code)
        ->set('sender.country', $sender->country)
        ->set('recipient.name', $recipient->name)
        ->set('recipient.email', $recipient->email)
        ->set('recipient.address', $recipient->address)
        ->set('recipient.city', $recipient->city)
        ->set('recipient.postal_code', $recipient->postal_code)
        ->set('recipient.country', $recipient->country)
        ->set('shipment.package_count', 1)
        ->set('shipment.shipment_type', 'parcel')
        ->set('shipment.service_type', 'express')
        ->set('shipment.origin', 'Paris, France')
        ->set('shipment.destination', 'Madrid, Spain')
        ->call('save')
        ->assertHasNoErrors();

    $shipment = Shipment::first();

    expect($shipment)->not->toBeNull()
        ->and($shipment->current_status)->toBe(ShipmentStatus::Registered)
        ->and($shipment->events()->count())->toBe(1);

    Notification::assertSentOnDemand(ShipmentStatusNotification::class);
});

test('an operator adding a tracking event updates the shipment status and history', function () {
    $operator = User::factory()->create();
    $this->actingAs($operator);

    $shipment = Shipment::factory()->create(['current_status' => ShipmentStatus::Registered]);

    Livewire::test('pages::admin.shipments.show', ['shipment' => $shipment])
        ->set('eventStatus', 'in_transit')
        ->set('eventLocation', 'Lyon, France')
        ->call('addEvent')
        ->assertHasNoErrors();

    expect($shipment->fresh()->current_status)->toBe(ShipmentStatus::InTransit)
        ->and($shipment->events()->count())->toBe(1);
});

test('the shipments index can be searched by tracking code', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $shipment = Shipment::factory()->create();
    $other = Shipment::factory()->create();

    Livewire::test('pages::admin.shipments.index')
        ->set('search', $shipment->tracking_code)
        ->assertSee($shipment->tracking_code)
        ->assertDontSee($other->tracking_code);
});
