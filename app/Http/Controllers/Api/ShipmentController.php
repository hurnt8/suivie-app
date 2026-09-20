<?php

namespace App\Http\Controllers\Api;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreShipmentRequest;
use App\Http\Requests\Api\UpdateShipmentStatusRequest;
use App\Http\Resources\ShipmentResource;
use App\Http\Resources\TrackingEventResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;

class ShipmentController extends Controller
{
    public function __construct(private readonly ShipmentService $shipmentService) {}

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $shipment = $this->shipmentService->create($request->validated());

        return (new ShipmentResource($shipment->load(['sender', 'recipient'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Shipment $shipment): ShipmentResource
    {
        return new ShipmentResource($shipment->load(['sender', 'recipient']));
    }

    public function status(Shipment $shipment): JsonResponse
    {
        return response()->json([
            'tracking_code' => $shipment->tracking_code,
            'status' => $shipment->current_status->value,
            'status_label' => $shipment->current_status->label(),
        ]);
    }

    public function events(Shipment $shipment): JsonResponse
    {
        return response()->json([
            'data' => TrackingEventResource::collection($shipment->events),
        ]);
    }

    public function updateStatus(UpdateShipmentStatusRequest $request, Shipment $shipment): ShipmentResource
    {
        $validated = $request->validated();

        $this->shipmentService->changeStatus(
            $shipment,
            ShipmentStatus::from($validated['status']),
            collect($validated)->except('status')->filter()->all(),
            $request->user(),
        );

        return new ShipmentResource($shipment->fresh(['sender', 'recipient']));
    }
}
