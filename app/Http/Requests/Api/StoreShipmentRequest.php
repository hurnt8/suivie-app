<?php

namespace App\Http\Requests\Api;

use App\Concerns\PartyValidationRules;
use App\Concerns\ShipmentValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    use PartyValidationRules, ShipmentValidationRules;

    public function authorize(): bool
    {
        return $this->user()->tokenCan('shipments:create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = $this->shipmentRules();

        $rules += filled($this->input('sender.id'))
            ? ['sender.id' => ['required', 'integer', 'exists:senders,id']]
            : $this->partyRules('sender');

        $rules += filled($this->input('recipient.id'))
            ? ['recipient.id' => ['required', 'integer', 'exists:recipients,id']]
            : $this->partyRules('recipient');

        return $rules;
    }
}
