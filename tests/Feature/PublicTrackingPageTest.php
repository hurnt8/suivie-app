<?php

use App\Models\Shipment;

test('the home page is accessible and shows the tracking form', function () {
    $response = $this->get(route('home'));

    $response->assertOk()->assertSee(route('tracking.search'), false);
});

test('a valid tracking code shows the shipment with masked personal data', function () {
    $shipment = Shipment::factory()->create();

    $response = $this->get(route('tracking.show', $shipment->tracking_code));

    $response->assertOk()
        ->assertSee($shipment->tracking_code)
        ->assertDontSee($shipment->sender->email)
        ->assertDontSee($shipment->recipient->email);
});

test('the amount communicated to the recipient is shown on the tracking page', function () {
    $shipment = Shipment::factory()->create(['amount' => 120.5, 'currency' => 'EUR']);

    $this->withSession(['locale' => 'fr'])
        ->get(route('tracking.show', $shipment->tracking_code))
        ->assertOk()
        ->assertSee('120,50');

    $this->withSession(['locale' => 'en'])
        ->get(route('tracking.show', $shipment->tracking_code))
        ->assertOk()
        ->assertSee('€120.50');
});

test('no amount block is rendered when the shipment has no amount', function () {
    $shipment = Shipment::factory()->create(['amount' => null, 'currency' => null]);

    $this->get(route('tracking.show', $shipment->tracking_code))
        ->assertOk()
        ->assertDontSee(__('site.amount'));
});

test('tracking lookup is case-insensitive', function () {
    $shipment = Shipment::factory()->create();

    $response = $this->get('/tracking/'.strtolower($shipment->tracking_code));

    $response->assertOk()->assertSee($shipment->tracking_code);
});

test('an unknown tracking code returns a 404', function () {
    $response = $this->get(route('tracking.show', 'LVR-2026-ZZZZZZ'));

    $response->assertNotFound();
});

test('searching redirects to the canonical tracking url', function () {
    $shipment = Shipment::factory()->create();

    $response = $this->get(route('tracking.search', ['code' => $shipment->tracking_code]));

    $response->assertRedirect(route('tracking.show', $shipment->tracking_code));
});

test('searching an unknown code redirects back with an error', function () {
    $response = $this->from(route('home'))->get(route('tracking.search', ['code' => 'DOES-NOT-EXIST']));

    $response->assertRedirect();
    $response->assertSessionHasErrors('code');
});

test('the public tracking endpoint is rate limited', function () {
    $shipment = Shipment::factory()->create();

    $response = null;

    for ($i = 0; $i < 31; $i++) {
        $response = $this->get(route('tracking.show', $shipment->tracking_code));
    }

    $response->assertStatus(429);
});
