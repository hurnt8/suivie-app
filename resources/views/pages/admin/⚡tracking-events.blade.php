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

<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('admin.events_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.events_subtitle') }}</flux:subheading>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('admin.events_search_placeholder')" class="max-w-sm" />
        <flux:select wire:model.live="status" :placeholder="__('admin.filter_status')" class="max-w-xs">
            <flux:select.option value="">{{ __('admin.filter_all_statuses') }}</flux:select.option>
            @foreach (ShipmentStatus::options() as $option)
                <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="card-elegant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
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
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.shipments.show', $event->shipment) }}" class="font-mono text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400" wire:navigate>
                                    {{ $event->shipment->tracking_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3"><flux:badge :color="$event->status->color()" size="sm">{{ $event->status->label() }}</flux:badge></td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $event->displayTitle() }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $event->location ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-zinc-400">{{ $event->event_date->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-xs text-zinc-400">{{ $event->createdBy?->name ?? __('admin.events_system') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->events->links() }}</div>
    </div>
</div>
