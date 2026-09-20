<?php

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Expéditions')] class extends Component {
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $status = '';

    #[Url(history: true)]
    public string $country = '';

    #[Url(history: true)]
    public string $service = '';

    #[Url(history: true)]
    public string $dateFrom = '';

    #[Url(history: true)]
    public string $dateTo = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedCountry(): void { $this->resetPage(); }
    public function updatedService(): void { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'country', 'service', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    #[Computed]
    public function shipments()
    {
        return Shipment::query()
            ->with(['sender', 'recipient'])
            ->search($this->search)
            ->filter([
                'status' => $this->status,
                'country' => $this->country,
                'service' => $this->service,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ])
            ->latest()
            ->paginate(15);
    }
}; ?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('admin.shipments_title') }}</flux:heading>
            <flux:subheading>{{ __('admin.shipments_subtitle') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.shipments.create') }}" variant="primary" icon="plus" wire:navigate>
            {{ __('admin.create_shipment_submit') }}
        </flux:button>
    </div>

    <div class="card-elegant p-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" :placeholder="__('admin.search_placeholder')" />
            </div>

            <flux:select wire:model.live="status" :placeholder="__('admin.filter_status')">
                <flux:select.option value="">{{ __('admin.filter_all_statuses') }}</flux:select.option>
                @foreach (ShipmentStatus::options() as $option)
                    <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="service" :placeholder="__('admin.filter_service')">
                <flux:select.option value="">{{ __('admin.filter_all_services') }}</flux:select.option>
                @foreach (ServiceType::options() as $option)
                    <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live="country" :placeholder="__('admin.filter_country')" />

            <div class="flex gap-2">
                <flux:input wire:model.live="dateFrom" type="date" />
                <flux:input wire:model.live="dateTo" type="date" />
            </div>
        </div>

        @if ($search || $status || $country || $service || $dateFrom || $dateTo)
            <div class="mt-3">
                <flux:button size="sm" variant="ghost" wire:click="resetFilters">{{ __('admin.filter_reset') }}</flux:button>
            </div>
        @endif
    </div>

    <div class="card-elegant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="table-head-elegant">
                    <tr class="text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                        <th class="px-4 py-3 font-semibold">{{ __('admin.tracking_number') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.sender') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.recipient') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.route') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.created_at') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse ($this->shipments as $shipment)
                        <tr wire:key="shipment-{{ $shipment->id }}" class="transition-colors hover:bg-zinc-50/80 dark:hover:bg-white/[0.03]">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.shipments.show', $shipment) }}" class="font-mono text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400" wire:navigate>
                                    {{ $shipment->tracking_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $shipment->sender->name }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $shipment->recipient->name }}</td>
                            <td class="px-4 py-3 text-xs text-zinc-400">{{ $shipment->origin }} → {{ $shipment->destination }}</td>
                            <td class="px-4 py-3"><flux:badge :color="$shipment->current_status->color()" size="sm">{{ $shipment->current_status->label() }}</flux:badge></td>
                            <td class="px-4 py-3 text-xs text-zinc-400">{{ $shipment->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('admin.no_shipments') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">
            {{ $this->shipments->links() }}
        </div>
    </div>
</div>
