@props(['settings'])

@php
    $company = $settings->company_name ?? config('app.name');
    $contactHref = route('home').'#contact';
    $navLinks = [
        ['label' => __('messages.nav_track'), 'href' => route('home').'#track'],
        ['label' => __('site.nav_services'), 'href' => route('home').'#services'],
        ['label' => __('messages.nav_how_it_works'), 'href' => route('home').'#how-it-works'],
        ['label' => __('site.nav_contact'), 'href' => $contactHref],
    ];
@endphp

<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-zinc-900 focus:shadow-lg">
    {{ __('site.skip_to_content') }}
</a>

{{-- Utility bar --}}
<div class="bg-brand-950 text-xs text-brand-100/80">
    <div class="mx-auto flex h-10 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <x-public.locale-switcher tone="dark" />

        <div class="flex items-center gap-5">
            @if (filled($settings->phone ?? null))
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->phone) }}" class="hidden items-center gap-1.5 transition-colors hover:text-white sm:inline-flex">
                    <flux:icon.phone class="size-3.5" />
                    {{ $settings->phone }}
                </a>
            @endif
            <a href="{{ $contactHref }}" class="transition-colors hover:text-white">{{ __('site.nav_contact') }}</a>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-semibold text-white hover:underline" wire:navigate>
                <flux:icon.user class="size-3.5" />
                {{ __('messages.nav_login') }}
            </a>
        </div>
    </div>
</div>

{{-- Main header --}}
<header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur dark:border-white/10 dark:bg-zinc-950/95" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" wire:navigate>
            <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-white shadow-[0_6px_16px_-6px_var(--color-brand-600)]">
                <x-app-logo-icon class="size-5" />
            </span>
            <span class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-white">{{ $company }}</span>
        </a>

        <nav class="hidden h-full items-stretch lg:flex" aria-label="{{ $company }}">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}" class="relative flex items-center px-4 text-sm font-semibold text-zinc-800 transition-colors after:absolute after:inset-x-4 after:bottom-0 after:h-0.5 after:origin-left after:scale-x-0 after:bg-orange-500 after:transition-transform hover:text-brand-700 hover:after:scale-x-100 dark:text-zinc-200 dark:hover:text-white">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            <div class="hidden lg:block">
                <flux:button href="{{ route('home') }}#track" variant="primary" color="orange" size="sm">
                    {{ __('messages.track_button') }}
                </flux:button>
            </div>

            <button type="button" class="-mr-2 inline-flex size-10 items-center justify-center rounded-lg text-zinc-700 hover:bg-zinc-100 lg:hidden dark:text-zinc-200 dark:hover:bg-white/10" @click="open = !open" :aria-expanded="open.toString()" aria-label="Menu">
                <flux:icon.bars-2 class="size-6" x-show="!open" />
                <flux:icon.x-mark class="size-6" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    {{-- Mobile panel --}}
    <div x-show="open" x-cloak x-transition.opacity @click.outside="open = false" class="absolute inset-x-0 top-full border-b border-zinc-200 bg-white shadow-xl lg:hidden dark:border-white/10 dark:bg-zinc-950">
        <nav class="mx-auto flex max-w-7xl flex-col px-4 py-3 sm:px-6" aria-label="{{ $company }}">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}" @click="open = false" class="rounded-lg px-3 py-3 text-base font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-100 dark:hover:bg-white/5">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <flux:button href="{{ route('home') }}#track" variant="primary" color="orange" class="mt-3 w-full">
                {{ __('messages.track_button') }}
            </flux:button>
        </nav>
    </div>
</header>
