<?php

use App\Models\DestinationRate;
use App\Models\User;
use Livewire\Livewire;

test('operators cannot open the rates page but admins can', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('admin.rates.index'))->assertForbidden();

    $this->actingAs(User::factory()->admin()->create());
    $this->get(route('admin.rates.index'))->assertOk();
});

test('an admin can add, edit and delete a rate', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test('pages::admin.rates.index')
        ->call('create')
        ->set('form.name', 'Espagne')
        ->set('form.base_amount', '11')
        ->set('form.per_kg_amount', '1.4')
        ->call('save')
        ->assertHasNoErrors();

    $rate = DestinationRate::firstOrFail();
    expect($rate->name)->toBe('Espagne')
        ->and($rate->base_amount)->toBe('11.00')
        ->and($rate->per_kg_amount)->toBe('1.40');

    Livewire::test('pages::admin.rates.index')
        ->call('edit', $rate->id)
        ->assertSet('form.name', 'Espagne')
        ->set('form.base_amount', '12.5')
        ->call('save')
        ->assertHasNoErrors();

    expect($rate->fresh()->base_amount)->toBe('12.50');

    Livewire::test('pages::admin.rates.index')->call('delete', $rate->id);

    expect(DestinationRate::count())->toBe(0);
});

test('rate names must be unique and amounts must be positive numbers', function () {
    $this->actingAs(User::factory()->admin()->create());
    DestinationRate::factory()->create(['name' => 'France']);

    Livewire::test('pages::admin.rates.index')
        ->call('create')
        ->set('form.name', 'France')
        ->set('form.base_amount', '-1')
        ->set('form.per_kg_amount', 'abc')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.base_amount', 'form.per_kg_amount']);

    expect(DestinationRate::count())->toBe(1);
});
