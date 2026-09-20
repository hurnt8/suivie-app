<?php

namespace App\Concerns;

use App\Enums\ServiceType;
use App\Enums\ShipmentType;
use Illuminate\Validation\Rule;

trait ShipmentValidationRules
{
    /**
     * @return array<string, mixed>
     */
    protected function shipmentRules(): array
    {
        return [
            'shipment.description' => ['nullable', 'string', 'max:1000'],
            'shipment.weight' => ['nullable', 'numeric', 'min:0.01', 'max:9999'],
            'shipment.package_count' => ['required', 'integer', 'min:1', 'max:999'],
            'shipment.shipment_type' => ['required', Rule::enum(ShipmentType::class)],
            'shipment.service_type' => ['required', Rule::enum(ServiceType::class)],
            'shipment.origin' => ['required', 'string', 'max:255'],
            'shipment.destination' => ['required', 'string', 'max:255'],
            'shipment.estimated_delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
            'shipment.special_instructions' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
