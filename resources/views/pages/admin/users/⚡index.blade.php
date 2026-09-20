<?php

use App\Enums\UserRole;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Utilisateurs')] class extends Component {
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'operator';
    public string $password = '';

    public function updatedSearch(): void { $this->resetPage(); }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);
    }

    public function create(): void
    {
        $this->authorize('create', User::class);
        $this->reset(['editingId', 'name', 'email', 'role', 'password']);
        $this->role = 'operator';
        Flux::modal('user-form')->show();
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->password = '';
        Flux::modal('user-form')->show();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
        ]);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $this->authorize('update', $user);
            $user->fill(['name' => $validated['name'], 'email' => $validated['email'], 'role' => $validated['role']]);
            if (filled($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
        } else {
            $this->authorize('create', User::class);
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);
        }

        Flux::modal('user-form')->close();
        Flux::toast(variant: 'success', text: __('admin.user_saved_toast'));
        unset($this->users);
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        $user->delete();

        Flux::toast(variant: 'success', text: __('admin.user_deleted_toast'));
        unset($this->users);
    }
}; ?>

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('admin.users_title') }}</flux:heading>
            <flux:subheading>{{ __('admin.users_subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create" class="w-full sm:w-auto">{{ __('admin.add_user') }}</flux:button>
    </div>

    <x-admin.filter-bar class="sm:max-w-md">
        <x-slot:search>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" clearable :placeholder="__('admin.search_placeholder')" />
        </x-slot:search>
    </x-admin.filter-bar>

    <div class="card-elegant table-card overflow-hidden">
        <table class="table-stack w-full text-sm">
            <thead class="table-head-elegant">
                <tr class="text-left text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400">
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_full_name') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_email') }}</th>
                    <th class="px-4 py-3 font-semibold">{{ __('admin.field_role') }}</th>
                    <th class="px-4 py-3"><span class="sr-only">{{ __('admin.actions') }}</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                @forelse ($this->users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="transition-colors hover:bg-zinc-50/80 dark:hover:bg-white/[0.03]">
                        <td class="cell-title px-4 py-3.5 font-medium text-zinc-800 dark:text-zinc-100">{{ $user->name }}</td>
                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400" data-label="{{ __('admin.field_email') }}">{{ $user->email }}</td>
                        <td class="px-4 py-3.5" data-label="{{ __('admin.field_role') }}"><flux:badge size="sm">{{ $user->role->label() }}</flux:badge></td>
                        <td class="cell-aside px-4 py-2 text-right whitespace-nowrap">
                            <flux:button size="sm" variant="ghost" icon="pencil" wire:click="edit({{ $user->id }})" :aria-label="__('admin.edit')" />
                            @if ($user->isNot(Auth::user()))
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete({{ $user->id }})" wire:confirm="{{ __('admin.confirm_delete') }}" :aria-label="__('admin.delete')" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-zinc-400">{{ __('admin.no_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($this->users->hasPages())
            <div class="border-t border-zinc-200/70 px-4 py-3 dark:border-white/10">{{ $this->users->links() }}</div>
        @endif
    </div>

    <flux:modal name="user-form" class="w-full max-w-lg">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('admin.edit_user') : __('admin.add_user') }}</flux:heading>

            <flux:input wire:model="name" :label="__('admin.field_full_name')" />
            <flux:input wire:model="email" type="email" :label="__('admin.field_email')" />

            <flux:select wire:model="role" :label="__('admin.field_role')">
                @foreach (UserRole::cases() as $case)
                    <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="password" type="password" :label="__('admin.field_password')" :description="$editingId ? __('admin.password_leave_blank') : null" />

            <div class="flex justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" x-on:click="$flux.modal('user-form').close()">{{ __('admin.cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('admin.save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
