<?php

namespace App\Http\Resources;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Shipment */
class ShipmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tracking_code' => $this->tracking_code,
            'status' => $this->current_status->value,
            'status_label' => $this->current_status->label(),
            'description' => $this->description,
            'weight' => $this->weight,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'mail_locale' => $this->mailLocale(),
            'package_count' => $this->package_count,
            'shipment_type' => $this->shipment_type->value,
            'service_type' => $this->service_type->value,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'estimated_delivery_date' => $this->estimated_delivery_date?->toDateString(),
            'shipped_at' => $this->shipped_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'sender' => [
                'name' => $this->sender->name,
                'email' => $this->sender->email,
            ],
            'recipient' => [
                'name' => $this->recipient->name,
                'email' => $this->recipient->email,
            ],
            'tracking_url' => $this->trackingUrl(),
            'created_at' => $this->created_at->toIso8601String(),
            'events' => TrackingEventResource::collection($this->whenLoaded('events')),
        ];
    }
}
