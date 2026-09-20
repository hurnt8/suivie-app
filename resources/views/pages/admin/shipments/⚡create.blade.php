<?php

use App\Concerns\PartyValidationRules;
use App\Concerns\ShipmentValidationRules;
use App\Enums\ServiceType;
use App\Enums\ShipmentType;
use App\Models\DestinationRate;
use App\Models\Recipient;
use App\Models\Sender;
use App\Models\Settings;
use App\Services\QuoteService;
use App\Services\ShipmentService;
use Flux\Flux;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Créer une expédition')] class extends Component {
    use PartyValidationRules, ShipmentValidationRules;

    public array $sender = ['id' => null, 'name' => '', 'company' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => '', 'postal_code' => '', 'country' => ''];
    public array $recipient = ['id' => null, 'name' => '', 'company' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => '', 'postal_code' => '', 'country' => ''];

    public array $shipment = [
        'description' => '',
        'weight' => null,
        'amount' => null,
        'package_count' => 1,
        'shipment_type' => 'parcel',
        'service_type' => 'standard',
        'origin' => '',
        'destination' => '',
        'estimated_delivery_date' => '',
        'special_instructions' => '',
        'mail_locale' => '',
    ];

    public bool $useExistingSender = false;
    public bool $useExistingRecipient = false;
    public string $senderSearch = '';
    public string $recipientSearch = '';

    /**
     * The amount the tariff last wrote into the field. While the field still holds
     * it, the amount follows the tariff; as soon as the operator edits it, it stays put.
     */
    public ?string $quotedAmount = null;

    public function mount(): void
    {
        $this->shipment['mail_locale'] = Settings::current()->default_locale;
    }

    /**
     * The tariff matching the current destination/weight, if any (quote depends on the destination).
     *
     * @return array{amount: float, rate: \App\Models\DestinationRate}|null
     */
    #[Computed]
    public function quote(): ?array
    {
        return app(QuoteService::class)->quote($this->shipment['destination'] ?? null, $this->shipment['weight'] ?? null);
    }

    /**
     * Livewire hook for any change under `shipment.*`.
     */
    public function updatedShipment(mixed $value, string $key): void
    {
        if (in_array($key, ['destination', 'weight'], true)) {
            unset($this->quote);

            if ($this->amountFollowsQuote()) {
                $this->fillAmountFromQuote();
            }
        }
    }

    /**
     * True while the operator hasn't overridden what the tariff put in the amount field.
     * (Livewire runs every "updated" hook after all properties of a request are set,
     * so this compares values rather than relying on which field changed first.)
     */
    protected function amountFollowsQuote(): bool
    {
        $current = $this->shipment['amount'] ?? null;
        $current = ($current === '' || $current === null) ? null : (float) $current;
        $quoted = $this->quotedAmount === null ? null : (float) $this->quotedAmount;

        return $current === $quoted;
    }

    /**
     * Destinations that have a tariff, offered as autocomplete suggestions.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    #[Computed]
    public function rateNames()
    {
        return DestinationRate::query()->orderBy('name')->pluck('name');
    }

    #[Computed]
    public function currency(): string
    {
        return Settings::current()->currency ?: 'EUR';
    }

    public function applyQuote(): void
    {
        unset($this->quote);
        $this->fillAmountFromQuote();
    }

    protected function fillAmountFromQuote(): void
    {
        $quote = $this->quote;

        $this->quotedAmount = $quote ? number_format($quote['amount'], 2, '.', '') : null;
        $this->shipment['amount'] = $this->quotedAmount;
    }

    /**
     * Human, translated field names for validation messages, so errors read
     * "Expéditeur – E-mail" instead of the raw "sender.email" key.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        $partyFields = [
            'name' => __('admin.field_full_name'),
            'company' => __('admin.field_company'),
            'email' => __('admin.field_email'),
            'phone' => __('admin.field_phone'),
            'address' => __('admin.field_address'),
            'city' => __('admin.field_city'),
            'postal_code' => __('admin.field_postal_code'),
            'country' => __('admin.field_country'),
        ];

        $attributes = [];

        foreach (['sender' => 'admin.section_sender', 'recipient' => 'admin.section_recipient'] as $prefix => $sectionKey) {
            foreach ($partyFields as $field => $label) {
                $attributes["{$prefix}.{$field}"] = __($sectionKey).' – '.$label;
            }
        }

        return $attributes + [
            'shipment.description' => __('admin.field_description'),
            'shipment.weight' => __('admin.field_weight'),
            'shipment.amount' => __('admin.field_amount'),
            'shipment.package_count' => __('admin.field_package_count'),
            'shipment.shipment_type' => __('admin.field_shipment_type'),
            'shipment.service_type' => __('admin.field_service_type'),
            'shipment.origin' => __('admin.field_origin'),
            'shipment.destination' => __('admin.field_destination'),
            'shipment.estimated_delivery_date' => __('admin.field_estimated_delivery'),
            'shipment.special_instructions' => __('admin.field_special_instructions'),
            'shipment.mail_locale' => __('admin.field_mail_locale'),
        ];
    }

    #[Computed]
    public function senderResults()
    {
        if ($this->senderSearch === '') {
            return collect();
        }

        return Sender::query()
            ->where('name', 'like', "%{$this->senderSearch}%")
            ->orWhere('email', 'like', "%{$this->senderSearch}%")
            ->limit(6)
            ->get();
    }

    #[Computed]
    public function recipientResults()
    {
        if ($this->recipientSearch === '') {
            return collect();
        }

        return Recipient::query()
            ->where('name', 'like', "%{$this->recipientSearch}%")
            ->orWhere('email', 'like', "%{$this->recipientSearch}%")
            ->limit(6)
            ->get();
    }

    public function toggleExisting(string $party): void
    {
        $property = 'useExisting'.ucfirst($party);
        $this->{$property} = ! $this->{$property};
        $this->{$party} = $this->blankParty();
    }

    public function selectParty(string $party, int $id): void
    {
        $record = $party === 'sender' ? Sender::findOrFail($id) : Recipient::findOrFail($id);

        $this->{$party} = Arr::only($record->toArray(), ['id', 'name', 'company', 'email', 'phone', 'address', 'city', 'postal_code', 'country']);
        $this->{$party.'Search'} = '';
    }

    public function clearParty(string $party): void
    {
        $this->{$party} = $this->blankParty();
    }

    protected function blankParty(): array
    {
        return ['id' => null, 'name' => '', 'company' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => '', 'postal_code' => '', 'country' => ''];
    }

    protected function formRules(): array
    {
        $rules = $this->shipmentRules();

        $rules += $this->useExistingSender
            ? ['sender.id' => ['required', 'exists:senders,id']]
            : $this->partyRules('sender');

        $rules += $this->useExistingRecipient
            ? ['recipient.id' => ['required', 'exists:recipients,id']]
            : $this->partyRules('recipient');

        return $rules;
    }

    public function save(ShipmentService $service): void
    {
        $this->validate($this->formRules());

        $shipment = $service->create([
            'sender' => $this->useExistingSender ? ['id' => $this->sender['id']] : Arr::except($this->sender, 'id'),
            'recipient' => $this->useExistingRecipient ? ['id' => $this->recipient['id']] : Arr::except($this->recipient, 'id'),
            'shipment' => Arr::except($this->shipment, []),
        ], Auth::user());

        Flux::toast(variant: 'success', text: __('admin.shipment_created_toast', ['code' => $shipment->tracking_code]));

        $this->redirect(route('admin.shipments.show', $shipment), navigate: true);
    }
}; ?>

<div class="max-w-4xl space-y-6">
    <div>
        <flux:heading size="xl">{{ __('admin.create_shipment_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.create_shipment_subtitle') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <x-admin.party-form
            prefix="sender"
            :title="__('admin.section_sender')"
            :model="$sender"
            :use-existing="$useExistingSender"
            :search="$senderSearch"
            :results="$this->senderResults"
        />

        <x-admin.party-form
            prefix="recipient"
            :title="__('admin.section_recipient')"
            :model="$recipient"
            :use-existing="$useExistingRecipient"
            :search="$recipientSearch"
            :results="$this->recipientResults"
        />

        <div class="card-elegant p-6">
            <flux:heading size="lg" class="mb-5">{{ __('admin.section_package') }}</flux:heading>

            <div class="grid grid-cols-1 items-start gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <flux:textarea wire:model="shipment.description" :label="__('admin.field_description')" rows="2" />
                </div>

                <flux:input wire:model.live.debounce.400ms="shipment.weight" type="number" step="0.01" :label="__('admin.field_weight')" />
                <flux:input wire:model="shipment.package_count" type="number" min="1" :label="__('admin.field_package_count')" />

                <flux:select wire:model="shipment.shipment_type" :label="__('admin.field_shipment_type')">
                    @foreach (ShipmentType::options() as $option)
                        <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="shipment.service_type" :label="__('admin.field_service_type')">
                    @foreach (ServiceType::options() as $option)
                        <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="shipment.origin" :label="__('admin.field_origin')" />
                <flux:input wire:model.live.debounce.400ms="shipment.destination" list="destination-rates" autocomplete="off" :label="__('admin.field_destination')" />

                <datalist id="destination-rates">
                    @foreach ($this->rateNames as $rateName)
                        <option value="{{ $rateName }}"></option>
                    @endforeach
                </datalist>

                <flux:input wire:model="shipment.estimated_delivery_date" type="date" :label="__('admin.field_estimated_delivery')" />

                <flux:field>
                    <flux:label>{{ __('admin.field_mail_locale') }}</flux:label>
                    <flux:select wire:model="shipment.mail_locale">
                        @foreach (\App\Support\Locales::NAMES as $code => $name)
                            <flux:select.option :value="$code">{{ $name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>{{ __('admin.field_mail_locale_hint') }}</flux:description>
                    <flux:error name="shipment.mail_locale" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('admin.field_amount') }} ({{ $this->currency }})</flux:label>
                    <flux:input wire:model.live.debounce.300ms="shipment.amount" type="number" step="0.01" min="0" inputmode="decimal" />
                    <flux:description>{{ __('admin.field_amount_hint') }}</flux:description>
                    <flux:error name="shipment.amount" />
                </flux:field>

                <div>
                    @if ($this->quote)
                        @php($money = fn (float $value): string => \Illuminate\Support\Number::currency($value, in: $this->currency, locale: app()->getLocale()))

                        <div class="rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 dark:border-brand-400/25 dark:bg-brand-400/10">
                            <p class="text-sm font-semibold text-brand-900 dark:text-brand-100">
                                {{ __('admin.quote_match', ['destination' => $this->quote['rate']->name, 'amount' => $money($this->quote['amount'])]) }}
                            </p>
                            <p class="mt-0.5 text-xs text-brand-700 dark:text-brand-200/80">
                                {{ __('admin.quote_formula', ['base' => $money((float) $this->quote['rate']->base_amount), 'per_kg' => $money((float) $this->quote['rate']->per_kg_amount)]) }}
                            </p>

                            @if (blank($shipment['amount'] ?? null) || abs((float) $shipment['amount'] - $this->quote['amount']) > 0.004)
                                <flux:button type="button" size="sm" class="mt-3" wire:click="applyQuote">{{ __('admin.quote_apply') }}</flux:button>
                            @endif
                        </div>
                    @elseif (filled($shipment['destination']))
                        <p class="text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ __('admin.quote_none') }}</p>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <flux:textarea wire:model="shipment.special_instructions" :label="__('admin.field_special_instructions')" rows="2" />
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('admin.shipments.index') }}" variant="ghost" wire:navigate>
                {{ __('admin.cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary">
                {{ __('admin.create_shipment_submit') }}
            </flux:button>
        </div>
    </form>
</div>
