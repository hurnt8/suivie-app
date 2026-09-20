<?php

namespace App\Models;

use Database\Factories\DestinationRateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Delivery tariff for one destination: quote = base_amount + per_kg_amount × weight.
 *
 * @property int $id
 * @property string $name
 * @property string $base_amount
 * @property string $per_kg_amount
 */
#[Fillable(['name', 'base_amount', 'per_kg_amount'])]
class DestinationRate extends Model
{
    /** @use HasFactory<DestinationRateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'base_amount' => 'decimal:2',
            'per_kg_amount' => 'decimal:2',
        ];
    }
}
