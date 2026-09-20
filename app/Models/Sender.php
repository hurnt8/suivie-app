<?php

namespace App\Models;

use App\Concerns\Maskable;
use Database\Factories\SenderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $company
 * @property string $email
 * @property string|null $phone
 * @property string $address
 * @property string $city
 * @property string $postal_code
 * @property string $country
 */
#[Fillable(['name', 'company', 'email', 'phone', 'address', 'city', 'postal_code', 'country'])]
class Sender extends Model
{
    /** @use HasFactory<SenderFactory> */
    use HasFactory;

    use Maskable;

    /**
     * @return HasMany<Shipment, $this>
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
