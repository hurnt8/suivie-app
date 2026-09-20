<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Database\Factories\TrackingEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shipment_id
 * @property ShipmentStatus $status
 * @property string $title
 * @property string|null $description
 * @property string|null $location
 * @property Carbon $event_date
 * @property int|null $created_by
 */
#[Fillable(['shipment_id', 'status', 'title', 'description', 'location', 'event_date', 'created_by'])]
class TrackingEvent extends Model
{
    /** @use HasFactory<TrackingEventFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'event_date' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isSystemGenerated(): bool
    {
        return $this->created_by === null;
    }

    /**
     * The `title`/`description` columns are written once, in whichever
     * locale was active at creation time. For system-generated events
     * (the vast majority — automatic status changes, seeded demo data)
     * that stored text would stay frozen in one language forever, so the
     * display layer re-derives it from the status enum instead, which is
     * always translated to the viewer's current locale. Manually-authored
     * events (an admin typed a custom title/description) keep their
     * literal text, since that's deliberate human-written content.
     */
    public function displayTitle(): string
    {
        return $this->isSystemGenerated() ? $this->status->label() : $this->title;
    }

    public function displayDescription(): ?string
    {
        return $this->isSystemGenerated() ? $this->status->description() : $this->description;
    }
}
