<?php

use App\Enums\ServiceType;

test('the home page is illustrated with real photos', function () {
    $response = $this->get(route('home'))->assertOk();

    foreach (['hero-port', 'network-warehouse', 'cta-port-night'] as $photo) {
        $response->assertSee("images/photos/{$photo}.jpg", false);
    }
});

test('every delivery service has its own photo on the home page', function () {
    $response = $this->get(route('home'))->assertOk();

    foreach (ServiceType::cases() as $service) {
        expect(public_path("images/photos/service-{$service->value}.jpg"))->toBeFile();

        $response->assertSee("images/photos/service-{$service->value}.jpg", false);
    }
});

test('every photo shipped with the site is a real, non-empty jpeg', function () {
    $photos = glob(public_path('images/photos/*.jpg'));

    expect($photos)->not->toBeEmpty();

    foreach ($photos as $photo) {
        [$width, $height] = getimagesize($photo);

        expect(filesize($photo))->toBeGreaterThan(30_000)
            ->and($width)->toBeGreaterThanOrEqual(900)
            ->and($height)->toBeGreaterThanOrEqual(500);
    }
});
