<?php

use App\Models\DestinationRate;
use App\Services\QuoteService;

test('the quote is the base amount plus the per-kg amount times the weight', function () {
    DestinationRate::factory()->create(['name' => 'France', 'base_amount' => 6.90, 'per_kg_amount' => 0.90]);

    $quote = app(QuoteService::class)->quote('Lyon, France', 3);

    expect($quote['amount'])->toBe(9.6)
        ->and($quote['rate']->name)->toBe('France');
});

test('the quote depends on the destination', function () {
    DestinationRate::factory()->create(['name' => 'France', 'base_amount' => 6.90, 'per_kg_amount' => 0.90]);
    DestinationRate::factory()->create(['name' => 'Japon', 'base_amount' => 38, 'per_kg_amount' => 5.60]);

    $service = app(QuoteService::class);

    expect($service->quote('Lyon, France', 2)['amount'])->toBe(8.7)
        ->and($service->quote('Tokyo, Japon', 2)['amount'])->toBe(49.2);
});

test('a missing or non-numeric weight prices the base amount only', function () {
    DestinationRate::factory()->create(['name' => 'France', 'base_amount' => 6.90, 'per_kg_amount' => 0.90]);

    $service = app(QuoteService::class);

    expect($service->quote('France')['amount'])->toBe(6.9)
        ->and($service->quote('France', '')['amount'])->toBe(6.9)
        ->and($service->quote('France', 'abc')['amount'])->toBe(6.9);
});

test('matching ignores case and accents', function () {
    DestinationRate::factory()->create(['name' => 'Etats-Unis', 'base_amount' => 32, 'per_kg_amount' => 4.80]);

    expect(app(QuoteService::class)->quote('new york, ÉTATS-UNIS', 1)['amount'])->toBe(36.8);
});

test('a rate only matches whole words', function () {
    DestinationRate::factory()->create(['name' => 'Niger', 'base_amount' => 20, 'per_kg_amount' => 3]);

    expect(app(QuoteService::class)->quote('Abuja, Nigeria', 1))->toBeNull();
});

test('the most specific rate wins when several match', function () {
    DestinationRate::factory()->create(['name' => 'Guinée', 'base_amount' => 20, 'per_kg_amount' => 3]);
    DestinationRate::factory()->create(['name' => 'Guinée équatoriale', 'base_amount' => 25, 'per_kg_amount' => 4]);

    $quote = app(QuoteService::class)->quote('Malabo, Guinée équatoriale', 1);

    expect($quote['rate']->name)->toBe('Guinée équatoriale')
        ->and($quote['amount'])->toBe(29.0);
});

test('there is no quote for an unknown or blank destination', function () {
    DestinationRate::factory()->create(['name' => 'France']);

    $service = app(QuoteService::class);

    expect($service->quote('Atlantide', 2))->toBeNull()
        ->and($service->quote('', 2))->toBeNull()
        ->and($service->quote(null, 2))->toBeNull();
});
