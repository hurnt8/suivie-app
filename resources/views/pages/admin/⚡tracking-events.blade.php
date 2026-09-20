<?php

use App\Enums\ShipmentStatus;
use App\Models\TrackingEvent;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Événements de suivi')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    /**
     * Events per status, for the counters on the status chips.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function statusCounts(): array
    {
        return TrackingEvent::query()
            ->toBase()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total) => (int) $total)
            ->all();
    }

    /**
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
    public function events()
    {
        return TrackingEvent::query()
            ->with(['shipment', 'createdBy'])
            ->when($this->search !== '', fn ($query) => $query->whereHas('shipment', fn ($q) => $q->where('tracking_code', 'like', "%{$this->search}%")))
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->latest('event_date')
            ->paginate(20);
    }
}; ?>

<div class="space-y-5">
    <div>
        <flux:heading size="xl">{{ __('admin.events_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.events_subtitle') }}</flux:subheading>
    </div>

    <x-admin.chips model="status" :selected="$status" :options="$this->statusChips" :all-count="array_sum($this->statusCounts)" />

    <x-admin.filter-bar>
        <x-slot:search>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" clearable :placeholder="__('admin.events_search_placeholder')" />
        </x-slot:search>
    </x-admin.filter-bar>

    <div class="card-elegant table-card overflow-hidden">
        <table class="table-stack w-full text-sm">
            <thead class="table-head-elegant">
                <tr class="text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                    <th class="px-4 py-3 font-semibold">{{ __('admin.tracking_number') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_status') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_event_title') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_event_location') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_event_date') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.events_created_by') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                @forelse ($this->events as $event)
                    <tr wire:key="event-{{ $event->id }}" class="transition-colors hover:bg-zinc-50/80 dark:hover:bg-white/[0.03]">
                        <td class="cell-title px-4 py-3.5 whitespace-nowrap">
                            <a href="{{ route('admin.shipments.show', $event->shipment) }}" class="font-mono text-[13px] font-semibold text-brand-700 hover:underline dark:text-brand-300" wire:navigate>
                                {{ $event->shipment->tracking_code }}
                            </a>
                        </td>
                        <td class="cell-aside px-4 py-3.5"><flux:badge :color="$event->status->color()" size="sm" class="md:whitespace-nowrap">{{ $event->status->label() }}</flux:badge></td>
                        <td class="px-4 py-3.5 text-zinc-700 dark:text-zinc-200" data-label="{{ __('admin.field_event_title') }}">{{ $event->displayTitle() }}</td>
                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.field_event_location') }}">{{ $event->location ?? '—' }}</td>
                        <td class="px-4 py-3.5 text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.field_event_date') }}">{{ $event->event_date->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3.5 text-xs text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.events_created_by') }}">{{ $event->createdBy?->name ?? __('admin.events_system') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($this->events->hasPages())
            <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->events->links() }}</div>
        @endif
    </div>
</div>
