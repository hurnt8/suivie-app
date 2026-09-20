<?php

use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Shipment;
use App\Services\TrackingCodeService;

test('generated tracking codes follow the PREFIX-YEAR-RANDOM format', function () {
    $code = app(TrackingCodeService::class)->generate('LVR');

    expect($code)->toMatch('/^LVR-'.now()->year.'-[A-Z0-9]{6}$/');
});

test('a custom prefix is respected', function () {
    $code = app(TrackingCodeService::class)->generate('AFG');

    expect($code)->toStartWith('AFG-'.now()->year.'-');
});

test('generated tracking codes are unique even when collisions occur', function () {
    $service = app(TrackingCodeService::class);

    $codes = collect(range(1, 25))->map(fn () => $service->generate('LVR'));

    expect($codes->unique())->toHaveCount(25);
});

test('it never reuses an existing tracking code', function () {
    $sender = Sender::factory()->create();

    $existing = app(TrackingCodeService::class)->generate('LVR');

    Shipment::factory()->create([
        'tracking_code' => $existing,
        'sender_id' => $sender->id,
        'recipient_id' => Recipient::factory()->create()->id,
    ]);

    $next = app(TrackingCodeService::class)->generate('LVR');

    expect($next)->not->toBe($existing);
});
