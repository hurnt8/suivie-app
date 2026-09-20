<?php

namespace App\Notifications\Shipment;

use App\Enums\NotificationChannel;
use App\Enums\ShipmentStatus;
use App\Mail\ShipmentStatusMail;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

/**
 * Notifies the shipment's sender by email whenever the shipment is created
 * or its status changes. `$shipmentNotificationId` links this dispatch back
 * to its `shipment_notifications` audit row so AppServiceProvider's
 * NotificationSent/NotificationFailed listeners can flip its status.
 *
 * Extension point: to add SMS or WhatsApp later, add cases to
 * NotificationChannel, return them from via() when a phone number is
 * available, and implement a matching toSms()/toWhatsApp() method — the
 * dispatch call site in ShipmentNotificationService does not need to change.
 */
class ShipmentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const AUDIENCE_SENDER = 'sender';

    public const AUDIENCE_RECIPIENT = 'recipient';

    public function __construct(
        public Shipment $shipment,
        public ShipmentStatus $status,
        public int $shipmentNotificationId,
        public string $audience = self::AUDIENCE_SENDER,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [NotificationChannel::Mail->value];
    }

    public function toMail(object $notifiable): Mailable
    {
        return (new ShipmentStatusMail($this->shipment, $this->status, $this->audience))
            ->to($notifiable->routeNotificationFor('mail', $this));
    }
}
