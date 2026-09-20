<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Settings;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Support\Locales;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes shipment creation and status/event management so that a
 * shipment's `current_status` and its `tracking_events` history can never
 * drift apart — every write goes through create() or changeStatus(), never
 * a raw ->update(['current_status' => ...]) from a controller/component.
 */
class ShipmentService
{
    public function __construct(
        private readonly TrackingCodeService $trackingCodeService,
        private readonly ShipmentNotificationService $notificationService,
        private readonly ActivityLogService $activityLog,
    ) {}

    /**
     * @param  array<string, array<string, mixed>>  $data  Keys `sender`, `recipient`, `shipment`; `sender`/`recipient`
     *                                                     are either `['id' => int]` to reuse an existing party or full field arrays to create a new one.
     */
    public function create(array $data, ?User $actor = null): Shipment
    {
        // Forms submit untouched optional inputs as '' — MySQL rejects that for
        // date/decimal columns, so blanks are stored as NULL instead.
        $senderData = $this->blankToNull($data['sender']);
        $recipientData = $this->blankToNull($data['recipient']);
        $shipmentData = $this->blankToNull($data['shipment']);

        $shipment = DB::transaction(function () use ($senderData, $recipientData, $shipmentData, $actor): Shipment {
            $sender = $this->resolveSender($senderData);
            $recipient = $this->resolveRecipient($recipientData);

            $shipment = Shipment::create([
                ...$shipmentData,
                // Snapshot the currency so a later Settings change never rewrites history.
                'currency' => ($shipmentData['amount'] ?? null) !== null ? Settings::current()->currency : null,
                // Snapshot the email language too, so a later Settings change doesn't switch it mid-shipment.
                'mail_locale' => Locales::isSupported($shipmentData['mail_locale'] ?? null)
                    ? $shipmentData['mail_locale']
                    : Settings::current()->default_locale,
                'tracking_code' => $this->trackingCodeService->generate(),
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'current_status' => ShipmentStatus::Registered,
            ]);

            $shipment->events()->create([
                'status' => ShipmentStatus::Registered,
                'title' => ShipmentStatus::Registered->label(),
                'description' => ShipmentStatus::Registered->description(),
                'location' => $shipment->origin,
                'event_date' => now(),
                'created_by' => $actor?->id,
            ]);

            $this->activityLog->log(
                action: 'shipment.created',
                description: __('messages.activity_shipment_created', ['tracking_code' => $shipment->tracking_code]),
                subject: $shipment,
            );

            return $shipment;
        });

        $this->notificationService->notifyStatus($shipment, ShipmentStatus::Registered);

        return $shipment;
    }

    /**
     * @param  array{title?: string, description?: string, location?: string, event_date?: \DateTimeInterface|string}  $eventData
     */
    public function changeStatus(Shipment $shipment, ShipmentStatus $status, array $eventData = [], ?User $actor = null): TrackingEvent
    {
        $event = DB::transaction(function () use ($shipment, $status, $eventData, $actor): TrackingEvent {
            $attributes = ['current_status' => $status];

            if ($status === ShipmentStatus::PickedUp && ! $shipment->shipped_at) {
                $attributes['shipped_at'] = now();
            }

            if ($status === ShipmentStatus::Delivered) {
                $attributes['delivered_at'] = now();
            }

            $shipment->update($attributes);

            $event = $shipment->events()->create([
                'status' => $status,
                'title' => $eventData['title'] ?? $status->label(),
                'description' => $eventData['description'] ?? $status->description(),
                'location' => $eventData['location'] ?? null,
                'event_date' => $eventData['event_date'] ?? now(),
                'created_by' => $actor?->id,
            ]);

            $this->activityLog->log(
                action: 'shipment.status_changed',
                description: __('messages.activity_shipment_status_changed', [
                    'tracking_code' => $shipment->tracking_code,
                    'status' => $status->label(),
                ]),
                subject: $shipment,
            );

            return $event;
        });

        $this->notificationService->notifyStatus($shipment->fresh(), $status);

        return $event;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function blankToNull(array $data): array
    {
        return array_map(
            fn (mixed $value): mixed => is_string($value) && trim($value) === '' ? null : $value,
            $data,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveSender(array $data): Sender
    {
        if (isset($data['id'])) {
            return Sender::findOrFail((int) $data['id']);
        }

        return Sender::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveRecipient(array $data): Recipient
    {
        if (isset($data['id'])) {
            return Recipient::findOrFail((int) $data['id']);
        }

        return Recipient::create($data);
    }
}
