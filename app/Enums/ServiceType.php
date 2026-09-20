<?php

namespace App\Enums;

enum ServiceType: string
{
    case Standard = 'standard';
    case Express = 'express';
    case Economy = 'economy';
    case International = 'international';

    public function label(): string
    {
        return __('enums.service_type.'.$this->value);
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
