{{--
    The toolbar above a list: a search field, and — optionally — a "Filters" button that unfolds a labelled panel.
    The panel starts open when a filter is already applied, so nothing is ever silently hidden.

    <x-admin.filter-bar :active-count="$n">
        <x-slot:search>  <flux:input … />  </x-slot:search>
        <x-slot:panel>   …labelled fields…  </x-slot:panel>       (optional)
        <x-slot:reset>   <flux:button …>    </x-slot:reset>       (optional, shown when active-count > 0)
    </x-admin.filter-bar>
--}}
@props(['activeCount' => 0])

<div x-data="{ open: @js($activeCount > 0) }" {{ $attributes->class('card-elegant') }}>
    <div class="flex items-center gap-2 p-3 sm:p-4">
        <div class="min-w-0 flex-1">
            {{ $search }}
        </div>

        @isset($panel)
            <flux:button type="button" variant="outline" icon="adjustments-horizontal" x-on:click="open = ! open" x-bind:aria-expanded="open" class="shrink-0">
                <span class="hidden sm:inline">{{ __('admin.filters') }}</span>
                @if ($activeCount > 0)
                    <span class="ms-1 inline-flex size-5 items-center justify-center rounded-full bg-accent text-[11px] font-bold text-white">{{ $activeCount }}</span>
                @endif
            </flux:button>
        @endisset
    </div>

    @isset($panel)
        <div x-show="open" x-cloak x-collapse>
            <div class="border-t border-zinc-200/70 p-3 sm:p-4 dark:border-white/10">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    {{ $panel }}
                </div>

                @if ($activeCount > 0 && isset($reset))
                    <div class="mt-4 flex justify-end">
                        {{ $reset }}
                    </div>
                @endif
            </div>
        </div>
    @endisset
</div>
