<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return __('enums.notification_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'zinc',
            self::Sent => 'emerald',
            self::Failed => 'red',
        };
    }
}
