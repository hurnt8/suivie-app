<?php

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('status chips count the shipments of each status and only list statuses that have some', function () {
    Shipment::factory()->count(2)->create(['current_status' => ShipmentStatus::InTransit]);
    Shipment::factory()->create(['current_status' => ShipmentStatus::Delivered]);

    $component = Livewire::test('pages::admin.shipments.index');

    expect($component->get('statusCounts'))->toMatchArray(['in_transit' => 2, 'delivered' => 1]);

    $chips = collect($component->get('statusChips'))->keyBy('value');

    expect($chips->keys()->all())->toEqualCanonicalizing(['in_transit', 'delivered'])
        ->and($chips['in_transit']['count'])->toBe(2);
});

test('clicking a status chip filters the list and the selected chip stays visible even when empty', function () {
    Shipment::factory()->count(2)->create(['current_status' => ShipmentStatus::InTransit]);
    Shipment::factory()->create(['current_status' => ShipmentStatus::Delivered]);

    $component = Livewire::test('pages::admin.shipments.index')->set('status', 'in_transit');

    expect($component->instance()->shipments->total())->toBe(2);

    $component->set('status', 'returned');

    expect($component->instance()->shipments->total())->toBe(0)
        ->and(collect($component->get('statusChips'))->pluck('value'))->toContain('returned');
});

test('only the filters of the unfolded panel count as active filters', function () {
    Livewire::test('pages::admin.shipments.index')
        ->assertSet('activeFilters', 0)
        ->set('search', 'LVR')
        ->set('status', 'delivered')
        ->assertSet('activeFilters', 0)
        ->set('service', 'express')
        ->set('country', 'France')
        ->assertSet('activeFilters', 2)
        ->call('resetFilters')
        ->assertSet('activeFilters', 0)
        ->assertSet('status', '')
        ->assertSet('search', '');
});

test('the list renders as a responsive table with labelled cells', function () {
    Shipment::factory()->create();

    Livewire::test('pages::admin.shipments.index')
        ->assertSeeHtml('table-card')
        ->assertSeeHtml('table-stack')
        ->assertSeeHtml('data-label="'.__('admin.recipient').'"')
        ->assertSee(__('admin.filters'));
});

test('the pagination footer only appears when there is more than one page', function () {
    Shipment::factory()->count(3)->create();

    $footer = 'border-t border-zinc-200/70 px-4 py-3';

    Livewire::test('pages::admin.shipments.index')->assertDontSeeHtml($footer);

    Shipment::factory()->count(15)->create();

    Livewire::test('pages::admin.shipments.index')->assertSeeHtml($footer);
});
