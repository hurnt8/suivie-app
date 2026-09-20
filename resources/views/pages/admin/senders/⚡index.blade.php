<?php

use App\Concerns\PartyValidationRules;
use App\Models\Sender;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Expéditeurs')] class extends Component {
    use PartyValidationRules, WithPagination;

    public string $search = '';

    public ?int $editingId = null;
    public array $form = ['name' => '', 'company' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => '', 'postal_code' => '', 'country' => ''];

    public function updatedSearch(): void { $this->resetPage(); }

    #[Computed]
    public function senders()
    {
        return Sender::query()
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->withCount('shipments')
            ->latest()
            ->paginate(15);
    }

    public function create(): void
    {
        $this->authorize('create', Sender::class);
        $this->editingId = null;
        $this->form = ['name' => '', 'company' => '', 'email' => '', 'phone' => '', 'address' => '', 'city' => '', 'postal_code' => '', 'country' => ''];
        Flux::modal('sender-form')->show();
    }

    public function edit(int $id): void
    {
        $sender = Sender::findOrFail($id);
        $this->authorize('update', $sender);
        $this->editingId = $id;
        $this->form = $sender->only(['name', 'company', 'email', 'phone', 'address', 'city', 'postal_code', 'country']);
        Flux::modal('sender-form')->show();
    }

    public function save(): void
    {
        $this->validate($this->partyRules('form'));

        if ($this->editingId) {
            $sender = Sender::findOrFail($this->editingId);
            $this->authorize('update', $sender);
            $sender->update($this->form);
        } else {
            $this->authorize('create', Sender::class);
            Sender::create($this->form);
        }

        Flux::modal('sender-form')->close();
        Flux::toast(variant: 'success', text: __('admin.sender_saved_toast'));
        unset($this->senders);
    }

    public function delete(int $id): void
    {
        $sender = Sender::findOrFail($id);
        $this->authorize('delete', $sender);

        if ($sender->shipments()->exists()) {
            Flux::toast(variant: 'danger', text: __('admin.party_delete_blocked'));

            return;
        }

        $sender->delete();
        Flux::toast(variant: 'success', text: __('admin.sender_deleted_toast'));
        unset($this->senders);
    }
}; ?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('admin.senders_title') }}</flux:heading>
            <flux:subheading>{{ __('admin.senders_subtitle') }}</flux:subheading>
        </div>
        @can('create', \App\Models\Sender::class)
            <flux:button variant="primary" icon="plus" wire:click="create">{{ __('admin.add_sender') }}</flux:button>
        @endcan
    </div>

    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('admin.search_placeholder')" class="max-w-sm" />

    <div class="card-elegant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="table-head-elegant">
                    <tr class="text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                        <th class="px-4 py-3 font-semibold">{{ __('admin.field_full_name') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.field_email') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.field_phone') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.location') }}</th>
                        <th class="px-4 py-3 font-semibold">{{ __('admin.shipments_count') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                    @forelse ($this->senders as $sender)
                        <tr wire:key="sender-{{ $sender->id }}" class="transition-colors hover:bg-zinc-50/80 dark:hover:bg-white/[0.03]">
                            <td class="px-4 py-3 font-medium text-zinc-800 dark:text-zinc-100">{{ $sender->name }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $sender->email }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $sender->phone }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $sender->city }}, {{ $sender->country }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $sender->shipments_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <flux:button size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $sender->id }})" />
                                @can('delete', $sender)
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete({{ $sender->id }})" wire:confirm="{{ __('admin.confirm_delete') }}" />
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->senders->links() }}</div>
    </div>

    <flux:modal name="sender-form" class="w-full max-w-lg">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('admin.edit_sender') : __('admin.add_sender') }}</flux:heading>

            <flux:input wire:model="form.name" :label="__('admin.field_full_name')" />
            <flux:input wire:model="form.company" :label="__('admin.field_company')" />
            <flux:input wire:model="form.email" type="email" :label="__('admin.field_email')" />
            <flux:input wire:model="form.phone" :label="__('admin.field_phone')" />
            <flux:input wire:model="form.address" :label="__('admin.field_address')" />
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.city" :label="__('admin.field_city')" />
                <flux:input wire:model="form.postal_code" :label="__('admin.field_postal_code')" />
            </div>
            <flux:input wire:model="form.country" :label="__('admin.field_country')" />

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" x-on:click="$flux.modal('sender-form').close()">{{ __('admin.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('admin.save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
