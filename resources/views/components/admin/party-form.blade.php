@props(['prefix', 'title', 'model', 'useExisting', 'search', 'results'])

<div class="card-elegant p-6">
    <div class="mb-5 flex items-center justify-between">
        <flux:heading size="lg">{{ $title }}</flux:heading>
        <flux:button size="sm" variant="ghost" wire:click="toggleExisting('{{ $prefix }}')">
            {{ $useExisting ? __('admin.party_create_new') : __('admin.party_use_existing') }}
        </flux:button>
    </div>

    @if ($useExisting)
        @if (blank($model['id'] ?? null))
            <div class="relative">
                <flux:input
                    wire:model.live.debounce.300ms="{{ $prefix }}Search"
                    :placeholder="__('admin.party_search_placeholder')"
                    icon="magnifying-glass"
                />

                @if ($search !== '' && $results->isNotEmpty())
                    <ul class="absolute z-20 mt-1 w-full overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800">
                        @foreach ($results as $result)
                            <li>
                                <button type="button" wire:click="selectParty('{{ $prefix }}', {{ $result->id }})"
                                        class="block w-full px-4 py-2.5 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                    <span class="block font-medium text-zinc-800 dark:text-zinc-100">{{ $result->name }}</span>
                                    <span class="block text-xs text-zinc-400">{{ $result->email }} &middot; {{ $result->city }}, {{ $result->country }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @elseif ($search !== '')
                    <p class="mt-2 text-xs text-zinc-400">{{ __('admin.party_no_results') }}</p>
                @endif
            </div>
        @else
            <div class="flex items-start justify-between rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $model['name'] }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $model['email'] }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $model['address'] }}, {{ $model['city'] }}, {{ $model['country'] }}</p>
                </div>
                <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="clearParty('{{ $prefix }}')" />
            </div>
        @endif
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <flux:input wire:model="{{ $prefix }}.name" :label="__('admin.field_full_name')" />
            <flux:input wire:model="{{ $prefix }}.company" :label="__('admin.field_company')" />
            <flux:input wire:model="{{ $prefix }}.email" type="email" :label="__('admin.field_email')" />
            <flux:input wire:model="{{ $prefix }}.phone" :label="__('admin.field_phone')" />
            <flux:input wire:model="{{ $prefix }}.address" :label="__('admin.field_address')" class="sm:col-span-2" />
            <flux:input wire:model="{{ $prefix }}.city" :label="__('admin.field_city')" />
            <flux:input wire:model="{{ $prefix }}.postal_code" :label="__('admin.field_postal_code')" />
            <flux:input wire:model="{{ $prefix }}.country" :label="__('admin.field_country')" class="sm:col-span-2" />
        </div>
    @endif
</div>
