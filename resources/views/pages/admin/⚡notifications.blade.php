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

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function statusCounts(): array
    {
        return ShipmentNotification::query()
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
        return collect(NotificationStatus::cases())
            ->map(fn (NotificationStatus $case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'count' => $this->statusCounts[$case->value] ?? 0,
            ])
            ->all();
    }

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

<div class="space-y-5">
    <div>
        <flux:heading size="xl">{{ __('admin.notifications_title') }}</flux:heading>
        <flux:subheading>{{ __('admin.notifications_subtitle') }}</flux:subheading>
    </div>

    <x-admin.chips model="status" :selected="$status" :options="$this->statusChips" :all-count="array_sum($this->statusCounts)" />

    <div class="card-elegant table-card overflow-hidden">
        <table class="table-stack w-full text-sm">
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
                        <td class="cell-title px-4 py-3.5 whitespace-nowrap">
                            <a href="{{ route('admin.shipments.show', $notification->shipment) }}" class="font-mono text-[13px] font-semibold text-brand-700 hover:underline dark:text-brand-300" wire:navigate>
                                {{ $notification->shipment->tracking_code }}
                            </a>
                        </td>
                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.notifications_recipient') }}">{{ $notification->recipient }}</td>
                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.notifications_channel') }}">{{ $notification->type->label() }}</td>
                        <td class="px-4 py-3.5 text-zinc-700 dark:text-zinc-200" data-label="{{ __('admin.notifications_subject') }}">{{ $notification->subject }}</td>
                        <td class="cell-aside px-4 py-3.5"><flux:badge :color="$notification->status->color()" size="sm" class="md:whitespace-nowrap">{{ $notification->status->label() }}</flux:badge></td>
                        <td class="px-4 py-3.5 text-xs whitespace-nowrap text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.notifications_sent_at') }}">{{ $notification->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($this->notifications->hasPages())
            <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->notifications->links() }}</div>
        @endif
    </div>
</div>
