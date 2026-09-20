{{--
    A row of pill filters bound to a Livewire property. It scrolls sideways on phones and wraps from `sm` up.

    <x-admin.chips model="status" :selected="$status" :options="[['value' => 'sent', 'label' => 'Sent', 'count' => 3]]" :all-count="12" />
--}}
@props([
    'model',
    'selected' => '',
    'options' => [],
    'allLabel' => null,
    'allCount' => null,
])

@php
    $base = 'inline-flex shrink-0 items-center gap-2 rounded-full border px-3.5 py-1.5 text-sm font-medium whitespace-nowrap transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent';
    $on = 'border-brand-950 bg-brand-950 text-white dark:border-white dark:bg-white dark:text-zinc-900';
    $off = 'border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 hover:bg-zinc-50 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-white/5';
@endphp

<div {{ $attributes->class('no-scrollbar overflow-x-auto pb-1 sm:overflow-visible sm:pb-0') }}>
    <div class="flex w-max gap-2 sm:w-auto sm:flex-wrap" role="group">
        <button
            type="button"
            wire:click="$set('{{ $model }}', '')"
            aria-pressed="{{ $selected === '' ? 'true' : 'false' }}"
            @class([$base, $on => $selected === '', $off => $selected !== ''])
        >
            {{ $allLabel ?? __('admin.filter_all') }}
            @if ($allCount !== null)
                <span class="text-xs tabular-nums opacity-70">{{ $allCount }}</span>
            @endif
        </button>

        @foreach ($options as $option)
            <button
                type="button"
                wire:key="chip-{{ $model }}-{{ $option['value'] }}"
                wire:click="$set('{{ $model }}', '{{ $option['value'] }}')"
                aria-pressed="{{ $selected === $option['value'] ? 'true' : 'false' }}"
                @class([$base, $on => $selected === $option['value'], $off => $selected !== $option['value']])
            >
                {{ $option['label'] }}
                @if (isset($option['count']))
                    <span class="text-xs tabular-nums opacity-70">{{ $option['count'] }}</span>
                @endif
            </button>
        @endforeach
    </div>
</div>
