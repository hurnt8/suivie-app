<?php

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Détail de l\'expédition')] class extends Component {
    public Shipment $shipment;

    public string $eventStatus = '';
    public string $eventTitle = '';
    public string $eventDescription = '';
    public string $eventLocation = '';
    public string $eventDate = '';

    public function mount(Shipment $shipment): void
    {
        $this->authorize('view', $shipment);

        $this->shipment = $shipment;
        $this->eventStatus = $shipment->current_status->value;
        $this->eventDate = now()->format('Y-m-d\TH:i');
        $this->loadRelations();
    }

    protected function loadRelations(): void
    {
        $this->shipment->load(['sender', 'recipient', 'notifications' => fn ($query) => $query->latest()]);
        $this->shipment->setRelation('events', $this->shipment->events()->orderByDesc('event_date')->get());
    }

    public function addEvent(ShipmentService $service): void
    {
        $this->authorize('addEvent', $this->shipment);

        $validated = $this->validate([
            'eventStatus' => ['required', Rule::enum(ShipmentStatus::class)],
            'eventTitle' => ['nullable', 'string', 'max:255'],
            'eventDescription' => ['nullable', 'string', 'max:1000'],
            'eventLocation' => ['nullable', 'string', 'max:255'],
            'eventDate' => ['required', 'date'],
        ]);

        $service->changeStatus($this->shipment, ShipmentStatus::from($validated['eventStatus']), [
            'title' => $validated['eventTitle'] ?: null,
            'description' => $validated['eventDescription'] ?: null,
            'location' => $validated['eventLocation'] ?: null,
            'event_date' => $validated['eventDate'],
        ], Auth::user());

        $this->shipment->refresh();
        $this->loadRelations();
        $this->reset(['eventTitle', 'eventDescription', 'eventLocation']);
        $this->eventDate = now()->format('Y-m-d\TH:i');

        Flux::toast(variant: 'success', text: __('admin.event_added_toast'));
    }
}; ?>

<div class="space-y-6">
    <div class="card-elegant flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <flux:heading size="xl" class="font-mono">{{ $shipment->tracking_code }}</flux:heading>
                <flux:badge :color="$shipment->current_status->color()">{{ $shipment->current_status->label() }}</flux:badge>
            </div>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('admin.created_on', ['date' => $shipment->created_at->format('d/m/Y H:i')]) }}
            </p>
        </div>
        <flux:button :href="$shipment->trackingUrl()" target="_blank" variant="ghost" icon="arrow-top-right-on-square">
            {{ __('admin.view_public_page') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-1">
            <div class="card-elegant p-6">
                <flux:heading size="sm" class="mb-3">{{ __('admin.section_sender') }}</flux:heading>
                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->sender->name }}</p>
                @if ($shipment->sender->company)
                    <p class="text-xs text-zinc-400">{{ $shipment->sender->company }}</p>
                @endif
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $shipment->sender->email }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $shipment->sender->phone }}</p>
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $shipment->sender->address }}, {{ $shipment->sender->postal_code }} {{ $shipment->sender->city }}, {{ $shipment->sender->country }}</p>
            </div>

            <div class="card-elegant p-6">
                <flux:heading size="sm" class="mb-3">{{ __('admin.section_recipient') }}</flux:heading>
                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->recipient->name }}</p>
                @if ($shipment->recipient->company)
                    <p class="text-xs text-zinc-400">{{ $shipment->recipient->company }}</p>
                @endif
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $shipment->recipient->email }}</p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $shipment->recipient->phone }}</p>
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $shipment->recipient->address }}, {{ $shipment->recipient->postal_code }} {{ $shipment->recipient->city }}, {{ $shipment->recipient->country }}</p>
            </div>

            <div class="card-elegant p-6">
                <flux:heading size="sm" class="mb-3">{{ __('admin.section_package') }}</flux:heading>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_description') }}</dt><dd class="text-right font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->description ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_weight') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->weight ? $shipment->weight.' kg' : '—' }}</dd></div>
                    @if ($shipment->hasAmount())
                        <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_amount') }}</dt><dd class="font-semibold text-zinc-900 dark:text-white">{{ $shipment->formattedAmount() }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_mail_locale') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ \App\Support\Locales::name($shipment->mailLocale()) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_package_count') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->package_count }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_shipment_type') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->shipment_type->label() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_service_type') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->service_type->label() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_origin') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->origin }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_destination') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->destination }}</dd></div>
                    <div class="flex justify-between"><dt class="text-zinc-400">{{ __('admin.field_estimated_delivery') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->estimated_delivery_date?->format('d/m/Y') ?? '—' }}</dd></div>
                    @if ($shipment->special_instructions)
                        <div><dt class="text-zinc-400">{{ __('admin.field_special_instructions') }}</dt><dd class="mt-1 font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->special_instructions }}</dd></div>
                    @endif
                </dl>
            </div>

            @if ($shipment->notifications->isNotEmpty())
                <div class="card-elegant p-6">
                    <flux:heading size="sm" class="mb-3">{{ __('admin.notifications_title') }}</flux:heading>
                    <ul class="space-y-2">
                        @foreach ($shipment->notifications as $notification)
                            <li class="flex items-center justify-between text-xs">
                                <span class="text-zinc-600 dark:text-zinc-300">{{ $notification->subject }}</span>
                                <flux:badge :color="$notification->status->color()" size="sm">{{ $notification->status->label() }}</flux:badge>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="space-y-6 lg:col-span-2">
            @can('addEvent', $shipment)
                <div class="card-elegant p-6">
                    <flux:heading size="lg" class="mb-4">{{ __('admin.add_event_title') }}</flux:heading>

                    <form wire:submit="addEvent" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <flux:select wire:model="eventStatus" :label="__('admin.field_status')">
                            @foreach (ShipmentStatus::options() as $option)
                                <flux:select.option :value="$option['value']">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="eventDate" type="datetime-local" :label="__('admin.field_event_date')" />

                        <flux:input wire:model="eventTitle" :label="__('admin.field_event_title')" :placeholder="__('admin.field_event_title_placeholder')" class="sm:col-span-2" />

                        <flux:textarea wire:model="eventDescription" :label="__('admin.field_event_description')" rows="2" class="sm:col-span-2" />

                        <flux:input wire:model="eventLocation" :label="__('admin.field_event_location')" :placeholder="__('admin.field_event_location_placeholder')" class="sm:col-span-2" />

                        <div class="sm:col-span-2">
                            <flux:button type="submit" variant="primary">{{ __('admin.add_event_submit') }}</flux:button>
                        </div>
                    </form>
                </div>
            @endcan

            <div class="card-elegant p-6">
                <flux:heading size="lg" class="mb-6">{{ __('admin.tracking_history') }}</flux:heading>

                @if ($shipment->events->isEmpty())
                    <p class="text-sm text-zinc-400">{{ __('admin.no_events') }}</p>
                @else
                    <x-tracking-timeline :events="$shipment->events" />
                @endif
            </div>
        </div>
    </div>
</div>
