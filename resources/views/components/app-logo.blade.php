@props([
    'sidebar' => false,
])

@php
    // The brand name and logo are configurable in the admin settings; the app name is only a fallback.
    $settings = \App\Models\Settings::current();
    $brand = $settings->company_name ?: config('app.name', 'Laravel');
    $logoUrl = filled($settings->logo_path) ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings->logo_path) : null;
@endphp

@if($logoUrl)
    {{-- Uploaded logo replaces icon + name, shown larger so it stays readable. --}}
    <a {{ $attributes->class('flex items-center') }}>
        <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="h-10 w-auto max-w-full object-contain">
    </a>
@elseif($sidebar)
    <flux:sidebar.brand :name="$brand" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-brand-600 text-white shadow-[0_6px_16px_-6px_var(--color-brand-600)]">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="$brand" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-brand-600 text-white shadow-[0_6px_16px_-6px_var(--color-brand-600)]">
            <x-app-logo-icon class="size-5" />
        </x-slot>
    </flux:brand>
@endif
