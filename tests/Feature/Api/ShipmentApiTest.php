<?php

use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Shipment;
use App\Models\User;

test('unauthenticated requests are rejected', function () {
    $this->getJson('/api/shipments/LVR-2026-000000')->assertUnauthorized();
});

test('a token without the right ability cannot create a shipment', function () {
    $user = User::factory()->admin()->create();
    $token = $user->createToken('test', ['shipments:update'])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/shipments', [])
        ->assertForbidden();
});

test('a shipment can be created via the API', function () {
    $user = User::factory()->admin()->create();
    $token = $user->createToken('test', ['shipments:create'])->plainTextToken;

    $payload = [
        'sender' => Sender::factory()->make()->toArray(),
        'recipient' => Recipient::factory()->make()->toArray(),
        'shipment' => [
            'package_count' => 2,
            'shipment_type' => 'parcel',
            'service_type' => 'express',
            'origin' => 'Paris, France',
            'destination' => 'Rome, Italy',
        ],
    ];

    $response = $this->withToken($token)->postJson('/api/shipments', $payload);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'registered')
        ->assertJsonStructure(['data' => ['tracking_code', 'tracking_url']]);

    expect(Shipment::count())->toBe(1);
});

test('a shipment status and events can be retrieved via the API', function () {
    $user = User::factory()->admin()->create();
    $token = $user->createToken('test', ['shipments:create'])->plainTextToken;

    $shipment = Shipment::factory()->create();

    $this->withToken($token)
        ->getJson('/api/shipments/'.$shipment->tracking_code.'/status')
        ->assertOk()
        ->assertJson(['tracking_code' => $shipment->tracking_code]);

    $this->withToken($token)
        ->getJson('/api/shipments/'.$shipment->tracking_code.'/events')
        ->assertOk();
});

test('a shipment status can be updated via the API', function () {
    $user = User::factory()->admin()->create();
    $token = $user->createToken('test', ['shipments:update'])->plainTextToken;

    $shipment = Shipment::factory()->create(['current_status' => 'registered']);

    $response = $this->withToken($token)->patchJson('/api/shipments/'.$shipment->tracking_code.'/status', [
        'status' => 'in_transit',
        'location' => 'Lyon, France',
    ]);

    $response->assertOk()->assertJsonPath('data.status', 'in_transit');

    expect($shipment->fresh()->current_status->value)->toBe('in_transit');
});
