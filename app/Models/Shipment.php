<?php

namespace App\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Enums\ShipmentType;
use Database\Factories\ShipmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $tracking_code
 * @property int $sender_id
 * @property int $recipient_id
 * @property string|null $description
 * @property float|null $weight
 * @property int $package_count
 * @property ShipmentType $shipment_type
 * @property ServiceType $service_type
 * @property string $origin
 * @property string $destination
 * @property ShipmentStatus $current_status
 * @property Carbon|null $estimated_delivery_date
 * @property string|null $special_instructions
 * @property Carbon|null $shipped_at
 * @property Carbon|null $delivered_at
 * @property-read Sender $sender
 * @property-read Recipient $recipient
 */
#[Fillable([
    'tracking_code', 'sender_id', 'recipient_id', 'description', 'weight', 'package_count',
    'shipment_type', 'service_type', 'origin', 'destination', 'current_status',
    'estimated_delivery_date', 'special_instructions', 'shipped_at', 'delivered_at',
])]
class Shipment extends Model
{
    /** @use HasFactory<ShipmentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'shipment_type' => ShipmentType::class,
            'service_type' => ServiceType::class,
            'current_status' => ShipmentStatus::class,
            'weight' => 'decimal:2',
            'package_count' => 'integer',
            'estimated_delivery_date' => 'date',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'tracking_code';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where($field ?? $this->getRouteKeyName(), strtoupper((string) $value))->first();
    }

    /**
     * @return BelongsTo<Sender, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Sender::class);
    }

    /**
     * @return BelongsTo<Recipient, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    /**
     * @return HasMany<TrackingEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_date');
    }

    /**
     * @return HasMany<ShipmentNotification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(ShipmentNotification::class);
    }

    public function latestEvent(): ?TrackingEvent
    {
        return $this->events()->latest('event_date')->first();
    }

    public function trackingUrl(): string
    {
        return route('tracking.show', $this->tracking_code);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query->where('tracking_code', 'like', "%{$term}%")
                ->orWhereHas('sender', function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                })
                ->orWhereHas('recipient', function (Builder $query) use ($term): void {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
        });
    }

    /**
     * @param  Builder<self>  $query
     * @param  array{status?: string, country?: string, service?: string, date_from?: string, date_to?: string}  $filters
     * @return Builder<self>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('current_status', $status))
            ->when($filters['service'] ?? null, fn (Builder $query, string $service) => $query->where('service_type', $service))
            ->when($filters['country'] ?? null, fn (Builder $query, string $country) => $query->where(function (Builder $query) use ($country): void {
                $query->where('origin', 'like', "%{$country}%")->orWhere('destination', 'like', "%{$country}%");
            }))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date));
    }
}
