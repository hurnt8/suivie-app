<?php

use App\Enums\NotificationStatus;
use App\Models\ShipmentNotification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Notifications')] class extends Component {
    use WithPagination;

    public string $status = '';

    public function updatedStatus(): void { $this->resetPage(); }

    #[Computed]
    public function notifications()
    {
        return ShipmentNotification::query()
            ->with('shipment')
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(20);
    }
}; ?>

<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('admin.notifications_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.notifications_subtitle') }}</flux:subheading>
    </div>

    <flux:select wire:model.live="status" :placeholder="__('admin.filter_status')" class="max-w-xs">
        <flux:select.option value="">{{ __('admin.filter_all_statuses') }}</flux:select.option>
        @foreach (NotificationStatus::cases() as $case)
            <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
        @endforeach
    </flux:select>

    <div class="card-elegant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="table-head-elegant">
                    <tr class="text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                        <th class="px-4 py-3 font-semibold">{{ __('admin.tracking_number') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.notifications_recipient') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.notifications_channel') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.notifications_subject') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.field_status') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.notifications_sent_at') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse ($this->notifications as $notification)
                        <tr wire:key="notification-{{ $notification->id }}" class="transition-colors hover:bg-zinc-50/80 dark:hover:bg-white/[0.03]">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.shipments.show', $notification->shipment) }}" class="font-mono text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400" wire:navigate>
                                    {{ $notification->shipment->tracking_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $notification->recipient }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $notification->type->label() }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $notification->subject }}</td>
                            <td class="px-4 py-3"><flux:badge :color="$notification->status->color()" size="sm">{{ $notification->status->label() }}</flux:badge></td>
                            <td class="px-4 py-3 text-xs text-zinc-400">{{ $notification->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->notifications->links() }}</div>
    </div>
</div>
