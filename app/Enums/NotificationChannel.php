<?php

namespace App\Enums;

/**
 * Channels a shipment notification can be sent through. Only `Mail` is
 * implemented today; `Sms` and `WhatsApp` are reserved so a future channel
 * can be added without reshaping the `shipment_notifications` table or the
 * ShipmentNotificationService dispatch logic.
 */
enum NotificationChannel: string
{
    case Mail = 'mail';
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return __('enums.notification_channel.'.$this->value);
    }
}
