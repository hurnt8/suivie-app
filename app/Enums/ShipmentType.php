<?php

namespace App\Enums;

enum ShipmentType: string
{
    case Document = 'document';
    case Parcel = 'parcel';
    case Pallet = 'pallet';
    case Fragile = 'fragile';

    public function label(): string
    {
        return __('enums.shipment_type.'.$this->value);
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
