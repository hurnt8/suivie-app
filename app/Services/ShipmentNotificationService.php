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
     * Every status change emails the sender: the customer is kept informed at
     * each step of the shipment, including sorting, customs, return and cancellation.
     */
    public function notifyStatus(Shipment $shipment, ShipmentStatus $status): void
    {
        $shipment->loadMissing(['sender', 'recipient']);

        $this->send($shipment, $status, $shipment->sender->email, ShipmentStatusNotification::AUDIENCE_SENDER);

        // The amount/quote on a shipment is communicated to the recipient too:
        // when one is set, the recipient gets the creation email with the tracking link.
        if ($status === ShipmentStatus::Registered && $shipment->hasAmount()) {
            $this->send($shipment, $status, $shipment->recipient->email, ShipmentStatusNotification::AUDIENCE_RECIPIENT);
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
