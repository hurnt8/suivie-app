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
     * Every status change emails both the sender and the recipient (who also
     * receives the tracking link and the waybill), at each step of the shipment,
     * including sorting, customs, return and cancellation.
     */
    public function notifyStatus(Shipment $shipment, ShipmentStatus $status): void
    {
        $shipment->loadMissing(['sender', 'recipient']);

        $senderEmail = $shipment->sender->email;
        $recipientEmail = $shipment->recipient->email;

        $this->send($shipment, $status, $senderEmail, ShipmentStatusNotification::AUDIENCE_SENDER);

        if (filled($recipientEmail) && strcasecmp((string) $recipientEmail, (string) $senderEmail) !== 0) {
            $this->send($shipment, $status, $recipientEmail, ShipmentStatusNotification::AUDIENCE_RECIPIENT);
        }
    }

    private function send(Shipment $shipment, ShipmentStatus $status, string $email, string $audience): void
    {
        $record = ShipmentNotification::create([
            'shipment_id' => $shipment->id,
            'recipient' => $email,
            'type' => NotificationChannel::Mail,
            'subject' => $status->label(),
            'status' => NotificationStatus::Pending,
        ]);

        // ->locale() makes the queued job render the email (subject, body, dates)
        // in the language chosen for this shipment, whatever the worker's default is.
        Notification::route('mail', $email)->notify(
            (new ShipmentStatusNotification($shipment, $status, $record->id, $audience))
                ->locale($shipment->mailLocale()),
        );
    }
}
