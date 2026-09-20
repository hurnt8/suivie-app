<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shipment_id
 * @property string $recipient
 * @property NotificationChannel $type
 * @property string $subject
 * @property NotificationStatus $status
 * @property Carbon|null $sent_at
 */
#[Fillable(['shipment_id', 'recipient', 'type', 'subject', 'status', 'sent_at'])]
class ShipmentNotification extends Model
{
    protected function casts(): array
    {
        return [
            'type' => NotificationChannel::class,
            'status' => NotificationStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
