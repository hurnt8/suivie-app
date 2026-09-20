<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Registered = 'registered';
    case PickedUp = 'picked_up';
    case SortingCenter = 'sorting_center';
    case InTransit = 'in_transit';
    case Customs = 'customs';
    case DestinationCenter = 'destination_center';
    case OutForDelivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Delayed = 'delayed';
    case Returned = 'returned';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __('enums.shipment_status.'.$this->value);
    }

    public function description(): string
    {
        return __('enums.shipment_status_description.'.$this->value);
    }

    /**
     * Tailwind/Flux color token used for badges and timeline markers.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'zinc',
            self::Registered => 'sky',
            self::PickedUp, self::SortingCenter, self::InTransit, self::Customs, self::DestinationCenter, self::OutForDelivery => 'amber',
            self::Delivered => 'emerald',
            self::Delayed => 'orange',
            self::Returned, self::Cancelled => 'red',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Returned, self::Cancelled], true);
    }

    /**
     * The "happy path" progression used to render the public tracking timeline.
     *
     * @return array<int, self>
     */
    public static function timelineSteps(): array
    {
        return [
            self::Registered,
            self::PickedUp,
            self::SortingCenter,
            self::InTransit,
            self::DestinationCenter,
            self::OutForDelivery,
            self::Delivered,
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status) => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }
}
