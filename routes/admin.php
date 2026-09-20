<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/', 'pages::admin.⚡dashboard')->name('dashboard');

    Route::livewire('shipments', 'pages::admin.shipments.⚡index')->name('shipments.index');
    Route::livewire('shipments/create', 'pages::admin.shipments.⚡create')->name('shipments.create');
    Route::livewire('shipments/{shipment:tracking_code}', 'pages::admin.shipments.⚡show')->name('shipments.show');

    Route::livewire('senders', 'pages::admin.senders.⚡index')->name('senders.index');
    Route::livewire('recipients', 'pages::admin.recipients.⚡index')->name('recipients.index');
    Route::livewire('tracking-events', 'pages::admin.⚡tracking-events')->name('tracking-events.index');
    Route::livewire('notifications', 'pages::admin.⚡notifications')->name('notifications.index');

    Route::middleware(['role:admin'])->group(function () {
        Route::livewire('users', 'pages::admin.users.⚡index')->name('users.index');
        Route::livewire('settings', 'pages::admin.⚡settings')->name('settings.edit');
    });
});
