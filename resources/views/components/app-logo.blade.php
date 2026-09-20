@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-brand-600 text-white shadow-[0_6px_16px_-6px_var(--color-brand-600)]">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-brand-600 text-white shadow-[0_6px_16px_-6px_var(--color-brand-600)]">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:brand>
@endif
