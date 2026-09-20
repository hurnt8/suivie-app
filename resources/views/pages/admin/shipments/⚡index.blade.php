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

    /**
     * How many filters of the unfolded panel are applied (the search box and the status chips are always visible).
     */
    #[Computed]
    public function activeFilters(): int
    {
        return collect([$this->service, $this->country, $this->dateFrom, $this->dateTo])->filter(fn (string $value) => $value !== '')->count();
    }

    /**
     * Shipments per status, for the counters on the status chips.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function statusCounts(): array
    {
        return Shipment::query()
            ->toBase()
            ->selectRaw('current_status, COUNT(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /**
     * The chips to show: every status that has shipments, plus the selected one even if it is empty.
     *
     * @return array<int, array{value: string, label: string, count: int}>
     */
    #[Computed]
    public function statusChips(): array
    {
        return collect(ShipmentStatus::options())
            ->map(fn (array $option) => $option + ['count' => $this->statusCounts[$option['value']] ?? 0])
            ->filter(fn (array $option) => $option['count'] > 0 || $option['value'] === $this->status)
            ->values()
            ->all();
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

<div class="space-y-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('admin.shipments_title') }}</flux:heading>
            <flux:subheading>{{ __('admin.shipments_subtitle') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.shipments.create') }}" variant="primary" icon="plus" wire:navigate class="w-full sm:w-auto">
            {{ __('admin.create_shipment_submit') }}
        </flux:button>
    </div>

    <x-admin.chips model="status" :selected="$status" :options="$this->statusChips" :all-count="array_sum($this->statusCounts)" />

    <x-admin.filter-bar :active-count="$this->activeFilters">
        <x-slot:search>
            <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" clearable :placeholder="__('admin.search_placeholder')" />
        </x-slot:search>

        <x-slot:panel>
            <flux:select wire:model.live="service" :label="__('admin.filter_service')">
                <flux:select.option value="">{{ __('admin.filter_all_services') }}</flux:select.option>
                @foreach (ServiceType::options() as $option)
                    <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live.debounce.400ms="country" :label="__('admin.filter_country')" />
            <flux:input wire:model.live="dateFrom" type="date" :label="__('admin.filter_date_from')" />
            <flux:input wire:model.live="dateTo" type="date" :label="__('admin.filter_date_to')" />
        </x-slot:panel>

        <x-slot:reset>
            <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="resetFilters">{{ __('admin.filter_reset') }}</flux:button>
        </x-slot:reset>
    </x-admin.filter-bar>

    <div class="card-elegant table-card overflow-hidden">
        <table class="table-stack w-full text-sm">
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
                        <td class="cell-title px-4 py-3.5 whitespace-nowrap">
                            <a href="{{ route('admin.shipments.show', $shipment) }}" class="font-mono text-[13px] font-semibold text-brand-700 hover:underline dark:text-brand-300" wire:navigate>
                                {{ $shipment->tracking_code }}
                            </a>
                        </td>
                        <td class="px-4 py-3.5 text-zinc-700 dark:text-zinc-200" data-label="{{ __('admin.sender') }}">{{ $shipment->sender->name }}</td>
                        <td class="px-4 py-3.5 text-zinc-700 dark:text-zinc-200" data-label="{{ __('admin.recipient') }}">{{ $shipment->recipient->name }}</td>
                        <td class="px-4 py-3.5 cell-clamp text-xs text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.route') }}">
                            <span title="{{ $shipment->origin }} → {{ $shipment->destination }}">{{ $shipment->origin }} → {{ $shipment->destination }}</span>
                        </td>
                        <td class="cell-aside px-4 py-3.5"><flux:badge :color="$shipment->current_status->color()" size="sm" class="md:whitespace-nowrap">{{ $shipment->current_status->label() }}</flux:badge></td>
                        <td class="px-4 py-3.5 text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.created_at') }}">{{ $shipment->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-400">{{ __('admin.no_shipments') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($this->shipments->hasPages())
            <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->shipments->links() }}</div>
        @endif
    </div>
</div>
