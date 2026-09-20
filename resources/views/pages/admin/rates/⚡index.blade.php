<?php

use App\Models\DestinationRate;
use App\Models\Settings;
use Flux\Flux;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tarifs par destination')] class extends Component {
    public ?int $editingId = null;
    public array $form = ['name' => '', 'base_amount' => '', 'per_kg_amount' => ''];

    #[Computed]
    public function rates()
    {
        return DestinationRate::query()->orderBy('name')->get();
    }

    #[Computed]
    public function currency(): string
    {
        return Settings::current()->currency ?: 'EUR';
    }

    public function money(mixed $amount): string
    {
        return Number::currency((float) $amount, in: $this->currency, locale: app()->getLocale());
    }

    protected function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:120', Rule::unique('destination_rates', 'name')->ignore($this->editingId)],
            'form.base_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'form.per_kg_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => __('admin.field_rate_destination'),
            'form.base_amount' => __('admin.field_rate_base'),
            'form.per_kg_amount' => __('admin.field_rate_per_kg'),
        ];
    }

    public function create(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->form = ['name' => '', 'base_amount' => '0', 'per_kg_amount' => '0'];
        Flux::modal('rate-form')->show();
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $rate = DestinationRate::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'name' => $rate->name,
            'base_amount' => (string) $rate->base_amount,
            'per_kg_amount' => (string) $rate->per_kg_amount,
        ];
        Flux::modal('rate-form')->show();
    }

    public function save(): void
    {
        $this->form['name'] = trim($this->form['name']);
        $this->validate();

        if ($this->editingId) {
            DestinationRate::findOrFail($this->editingId)->update($this->form);
        } else {
            DestinationRate::create($this->form);
        }

        Flux::modal('rate-form')->close();
        Flux::toast(variant: 'success', text: __('admin.rate_saved_toast'));
        unset($this->rates);
    }

    public function delete(int $id): void
    {
        DestinationRate::findOrFail($id)->delete();

        Flux::toast(variant: 'success', text: __('admin.rate_deleted_toast'));
        unset($this->rates);
    }
}; ?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('admin.rates_title') }}</flux:heading>
            <flux:subheading>{{ __('admin.rates_subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create" class="w-full sm:w-auto">{{ __('admin.add_rate') }}</flux:button>
    </div>

    <div class="card-elegant table-card overflow-hidden">
        <table class="table-stack w-full text-sm">
            <thead class="table-head-elegant">
                <tr class="text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_rate_destination') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_rate_base') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_rate_per_kg') }}</th>
                    <th class="px-4 py-3"><span class="sr-only">{{ __('admin.actions') }}</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                @forelse ($this->rates as $rate)
                    <tr wire:key="rate-{{ $rate->id }}" class="transition-colors hover:bg-zinc-50/80 dark:hover:bg-white/[0.03]">
                        <td class="cell-title px-4 py-3.5 font-medium text-zinc-800 dark:text-zinc-100">{{ $rate->name }}</td>
                        <td class="px-4 py-3.5 text-zinc-600 tabular-nums dark:text-zinc-300" data-label="{{ __('admin.field_rate_base') }}">{{ $this->money($rate->base_amount) }}</td>
                        <td class="px-4 py-3.5 text-zinc-600 tabular-nums dark:text-zinc-300" data-label="{{ __('admin.field_rate_per_kg') }}">{{ $this->money($rate->per_kg_amount) }}</td>
                        <td class="cell-aside px-4 py-2 text-right whitespace-nowrap">
                            <flux:button size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $rate->id }})" :aria-label="__('admin.edit')" />
                            <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete({{ $rate->id }})" wire:confirm="{{ __('admin.confirm_delete') }}" :aria-label="__('admin.delete')" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <flux:modal name="rate-form" class="w-full max-w-lg">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('admin.edit_rate') : __('admin.add_rate') }}</flux:heading>

            <flux:input wire:model="form.name" :label="__('admin.field_rate_destination')" :description="__('admin.field_rate_destination_hint')" />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="form.base_amount" type="number" step="0.01" min="0" inputmode="decimal" :label="__('admin.field_rate_base').' ('.$this->currency.')'" />
                <flux:input wire:model="form.per_kg_amount" type="number" step="0.01" min="0" inputmode="decimal" :label="__('admin.field_rate_per_kg').' ('.$this->currency.')'" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" x-on:click="$flux.modal('rate-form').close()">{{ __('admin.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('admin.save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
