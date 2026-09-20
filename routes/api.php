<?php

use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('shipments')->name('api.shipments.')->group(function () {
    Route::post('/', [ShipmentController::class, 'store'])->name('store');
    Route::get('{shipment:tracking_code}', [ShipmentController::class, 'show'])->name('show');
    Route::get('{shipment:tracking_code}/status', [ShipmentController::class, 'status'])->name('status');
    Route::get('{shipment:tracking_code}/events', [ShipmentController::class, 'events'])->name('events');
    Route::patch('{shipment:tracking_code}/status', [ShipmentController::class, 'updateStatus'])->name('update-status');
});
