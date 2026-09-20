<?php

use App\Enums\ShipmentStatus;
use App\Models\DestinationRate;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Settings;
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

test('the amount follows the destination tariff until the operator overrides it', function () {
    $this->actingAs(User::factory()->admin()->create());
    DestinationRate::factory()->create(['name' => 'France', 'base_amount' => 6.90, 'per_kg_amount' => 0.90]);

    Livewire::test('pages::admin.shipments.create')
        ->set('shipment.weight', '3')
        ->set('shipment.destination', 'Lyon, France')
        ->assertSet('shipment.amount', '9.60')
        ->set('shipment.weight', '10')
        ->assertSet('shipment.amount', '15.90')
        // Once the operator types their own amount, later changes no longer overwrite it…
        ->set('shipment.amount', '99')
        ->set('shipment.weight', '4')
        ->assertSet('shipment.amount', '99')
        ->assertSee(__('admin.quote_apply'))
        // …until they explicitly ask for the tariff again.
        ->call('applyQuote')
        ->assertSet('shipment.amount', '10.50')
        ->assertDontSee(__('admin.quote_apply'))
        ->set('shipment.destination', 'Atlantide')
        ->assertSet('shipment.amount', null)
        ->assertSee(__('admin.quote_none'));
});

test('the email language defaults to the platform language and is saved with the shipment', function () {
    Notification::fake();
    Settings::query()->updateOrCreate(['id' => 1], ['default_locale' => 'de']);
    Settings::flush();

    $this->actingAs(User::factory()->admin()->create());

    $sender = Sender::factory()->make();
    $recipient = Recipient::factory()->make();

    Livewire::test('pages::admin.shipments.create')
        ->assertSet('shipment.mail_locale', 'de')
        ->set('sender', ['id' => null, ...$sender->only(['name', 'company', 'email', 'phone', 'address', 'city', 'postal_code', 'country'])])
        ->set('recipient', ['id' => null, ...$recipient->only(['name', 'company', 'email', 'phone', 'address', 'city', 'postal_code', 'country'])])
        ->set('shipment.origin', 'Paris, France')
        ->set('shipment.destination', 'Madrid, Spain')
        ->set('shipment.mail_locale', 'es')
        ->call('save')
        ->assertHasNoErrors();

    expect(Shipment::firstOrFail()->mail_locale)->toBe('es');

    Notification::assertSentOnDemand(
        ShipmentStatusNotification::class,
        fn (ShipmentStatusNotification $notification) => $notification->locale === 'es',
    );
});

test('an unsupported email language is rejected by the form', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test('pages::admin.shipments.create')
        ->set('shipment.origin', 'Paris, France')
        ->set('shipment.destination', 'Madrid, Spain')
        ->set('shipment.mail_locale', 'xx')
        ->call('save')
        ->assertHasErrors(['shipment.mail_locale']);
});
