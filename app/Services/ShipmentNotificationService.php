<?php

namespace App\Services;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\ShipmentNotification;
use App\Notifications\Shipment\ShipmentStatusNotification;
use Illuminate\Support\Facades\Notification;

class ShipmentNotificationService
{
    /**
     * Statuses that warrant emailing the sender (§10 of the spec). Silent,
     * non-customer-facing statuses (pending, sorting_center, customs,
     * returned, cancelled) are intentionally excluded.
     *
     * @var array<int, ShipmentStatus>
     */
    private const NOTIFIABLE_STATUSES = [
        ShipmentStatus::Registered,
        ShipmentStatus::PickedUp,
        ShipmentStatus::InTransit,
        ShipmentStatus::DestinationCenter,
        ShipmentStatus::OutForDelivery,
        ShipmentStatus::Delivered,
        ShipmentStatus::Delayed,
    ];

    public function notifyStatus(Shipment $shipment, ShipmentStatus $status): void
    {
        if (! in_array($status, self::NOTIFIABLE_STATUSES, true)) {
            return;
        }

        $shipment->loadMissing('sender');

        $record = ShipmentNotification::create([
            'shipment_id' => $shipment->id,
            'recipient' => $shipment->sender->email,
            'type' => NotificationChannel::Mail,
            'subject' => $status->label(),
            'status' => NotificationStatus::Pending,
        ]);

        Notification::route('mail', $shipment->sender->email)
            ->notify(new ShipmentStatusNotification($shipment, $status, $record->id));
    }
}
