<?php

use App\Concerns\PartyValidationRules;
use App\Concerns\ShipmentValidationRules;
use App\Enums\ServiceType;
use App\Enums\ShipmentType;
use App\Models\Recipient;
use App\Models\Sender;
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
        'package_count' => 1,
        'shipment_type' => 'parcel',
        'service_type' => 'standard',
        'origin' => '',
        'destination' => '',
        'estimated_delivery_date' => '',
        'special_instructions' => '',
    ];

    public bool $useExistingSender = false;
    public bool $useExistingRecipient = false;
    public string $senderSearch = '';
    public string $recipientSearch = '';

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

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:textarea wire:model="shipment.description" :label="__('admin.field_description')" class="sm:col-span-2" rows="2" />

                <flux:input wire:model="shipment.weight" type="number" step="0.01" :label="__('admin.field_weight')" />
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
                <flux:input wire:model="shipment.destination" :label="__('admin.field_destination')" />

                <flux:input wire:model="shipment.estimated_delivery_date" type="date" :label="__('admin.field_estimated_delivery')" />

                <flux:textarea wire:model="shipment.special_instructions" :label="__('admin.field_special_instructions')" class="sm:col-span-2" rows="2" />
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
