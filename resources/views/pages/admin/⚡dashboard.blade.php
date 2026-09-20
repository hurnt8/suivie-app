<?php

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tableau de bord')] class extends Component {
    #[Computed]
    public function stats(): array
    {
        return Cache::remember('admin.dashboard.stats', 60, function () {
            return [
                'total' => Shipment::count(),
                'in_transit' => Shipment::whereIn('current_status', [
                    ShipmentStatus::PickedUp->value,
                    ShipmentStatus::SortingCenter->value,
                    ShipmentStatus::InTransit->value,
                    ShipmentStatus::Customs->value,
                    ShipmentStatus::DestinationCenter->value,
                    ShipmentStatus::OutForDelivery->value,
                ])->count(),
                'delivered' => Shipment::where('current_status', ShipmentStatus::Delivered->value)->count(),
                'pending' => Shipment::whereIn('current_status', [
                    ShipmentStatus::Pending->value,
                    ShipmentStatus::Registered->value,
                ])->count(),
                'returned' => Shipment::whereIn('current_status', [
                    ShipmentStatus::Returned->value,
                    ShipmentStatus::Cancelled->value,
                ])->count(),
            ];
        });
    }

    #[Computed]
    public function recentShipments()
    {
        return Shipment::with(['sender', 'recipient'])->latest()->take(6)->get();
    }

    #[Computed]
    public function recentEvents()
    {
        return TrackingEvent::with('shipment')->latest('event_date')->take(8)->get();
    }
}; ?>

<div class="space-y-8">
    <div>
        <flux:heading size="xl">{{ __('admin.dashboard_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.dashboard_subtitle') }}</flux:subheading>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ([
            ['label' => __('admin.stat_total'), 'value' => $this->stats['total'], 'icon' => 'archive-box', 'tint' => 'zinc'],
            ['label' => __('admin.stat_in_transit'), 'value' => $this->stats['in_transit'], 'icon' => 'truck', 'tint' => 'amber'],
            ['label' => __('admin.stat_delivered'), 'value' => $this->stats['delivered'], 'icon' => 'check-circle', 'tint' => 'emerald'],
            ['label' => __('admin.stat_pending'), 'value' => $this->stats['pending'], 'icon' => 'clock', 'tint' => 'sky'],
            ['label' => __('admin.stat_returned'), 'value' => $this->stats['returned'], 'icon' => 'arrow-uturn-left', 'tint' => 'red'],
        ] as $card)
            <div @class([
                'card-elegant p-5 transition-shadow hover:shadow-[var(--shadow-elegant-lg)]',
                'col-span-2 sm:col-span-1' => $loop->first,
            ])>
                <span @class([
                    'flex size-10 items-center justify-center rounded-xl',
                    'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300' => $card['tint'] === 'zinc',
                    'bg-amber-50 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400' => $card['tint'] === 'amber',
                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-400/10 dark:text-emerald-400' => $card['tint'] === 'emerald',
                    'bg-sky-50 text-sky-600 dark:bg-sky-400/10 dark:text-sky-400' => $card['tint'] === 'sky',
                    'bg-red-50 text-red-600 dark:bg-red-400/10 dark:text-red-400' => $card['tint'] === 'red',
                ])>
                    <flux:icon :icon="$card['icon']" class="size-5" />
                </span>
                <p class="mt-4 text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $card['value'] }}</p>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="card-elegant p-5 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="lg">{{ __('admin.recent_shipments') }}</flux:heading>
                <flux:button href="{{ route('admin.shipments.index') }}" variant="ghost" size="sm" wire:navigate>
                    {{ __('admin.view_all') }}
                </flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-head-elegant rounded-lg text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                            <th class="rounded-l-lg px-3 py-2.5 font-semibold">{{ __('admin.tracking_number') }}</th>
                            <th class="px-3 py-2.5 font-semibold">{{ __('admin.recipient') }}</th>
                            <th class="rounded-r-lg px-3 py-2.5 font-semibold">{{ __('admin.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->recentShipments as $shipment)
                            <tr class="border-b border-zinc-100 last:border-0 dark:border-white/5">
                                <td class="px-3 py-3">
                                    <a href="{{ route('admin.shipments.show', $shipment) }}" class="font-mono text-xs font-semibold text-brand-700 hover:underline dark:text-brand-300" wire:navigate>
                                        {{ $shipment->tracking_code }}
                                    </a>
                                </td>
                                <td class="px-3 py-3 text-zinc-600 dark:text-zinc-300">{{ $shipment->recipient->name }}</td>
                                <td class="px-3 py-3">
                                    <flux:badge :color="$shipment->current_status->color()" size="sm">{{ $shipment->current_status->label() }}</flux:badge>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-zinc-400">{{ __('admin.no_shipments') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-elegant p-5">
            <flux:heading size="lg" class="mb-4">{{ __('admin.recent_events') }}</flux:heading>

            <ul class="space-y-1">
                @forelse ($this->recentEvents as $event)
                    <li class="flex items-start gap-3 rounded-lg px-2 py-2 -mx-2 transition-colors hover:bg-zinc-50 dark:hover:bg-white/5">
                        <span @class([
                            'mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full',
                            'bg-emerald-100 text-emerald-600 dark:bg-emerald-400/15 dark:text-emerald-400' => $event->status->color() === 'emerald',
                            'bg-amber-100 text-amber-600 dark:bg-amber-400/15 dark:text-amber-400' => $event->status->color() === 'amber',
                            'bg-sky-100 text-sky-600 dark:bg-sky-400/15 dark:text-sky-400' => $event->status->color() === 'sky',
                            'bg-orange-100 text-orange-600 dark:bg-orange-400/15 dark:text-orange-400' => $event->status->color() === 'orange',
                            'bg-red-100 text-red-600 dark:bg-red-400/15 dark:text-red-400' => $event->status->color() === 'red',
                            'bg-zinc-100 text-zinc-500 dark:bg-white/10 dark:text-zinc-400' => $event->status->color() === 'zinc',
                        ])>
                            <span class="size-1.5 rounded-full bg-current"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $event->displayTitle() }}</p>
                            <p class="text-xs text-zinc-400">{{ $event->shipment->tracking_code }} &middot; {{ $event->event_date->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-zinc-400">{{ __('admin.no_events') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
