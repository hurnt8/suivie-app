<?php

use App\Models\Settings;
use App\Models\User;

test('the login page shows the configured company name, a real photo and the way back to the site', function () {
    Settings::query()->updateOrCreate(['id' => 1], ['company_name' => 'Acme Logistics']);
    Settings::flush();

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Acme Logistics')
        ->assertSee('images/photos/hero-port.jpg', false)
        ->assertSee(route('home'), false)
        ->assertSee(__('admin.nav_public_site'))
        ->assertSee(route('password.request'), false);
});

test('every authentication screen uses the same split layout', function () {
    foreach ([route('login'), route('password.request')] as $url) {
        $this->get($url)->assertOk()->assertSee('images/photos/hero-port.jpg', false);
    }
});

test('the page title and the admin sidebar use the configured company name', function () {
    Settings::query()->updateOrCreate(['id' => 1], ['company_name' => 'Acme Logistics']);
    Settings::flush();

    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('<title>', false)
        ->assertSee('Acme Logistics');
});
