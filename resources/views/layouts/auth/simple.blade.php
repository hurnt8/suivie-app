@php
    $company = \App\Models\Settings::current()->company_name ?: config('app.name', 'Laravel');

    $highlights = [
        __('messages.feature_global'),
        __('messages.feature_realtime'),
        __('messages.feature_secure'),
        __('messages.feature_support'),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-svh bg-white antialiased dark:bg-zinc-950">
        <div class="grid min-h-svh lg:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)]">

            {{-- Brand panel (desktop): a real photo, the company's promise and its guarantees. --}}
            <aside class="relative isolate hidden overflow-hidden bg-brand-950 text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16">
                <img src="{{ asset('images/photos/hero-port.jpg') }}" alt="" width="1920" height="1080" decoding="async" class="absolute inset-0 -z-10 size-full object-cover object-[68%_center]">
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-950 via-brand-950/70 to-brand-950/45"></div>
                <div class="absolute inset-x-0 bottom-0 -z-10 h-1 bg-gradient-to-r from-orange-500 via-orange-400 to-transparent"></div>

                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 self-start" wire:navigate>
                    <span class="flex size-11 items-center justify-center rounded-xl bg-brand-600 text-white shadow-[0_8px_20px_-6px_var(--color-brand-500)]">
                        <x-app-logo-icon class="size-6" />
                    </span>
                    <span class="text-lg font-extrabold tracking-tight">{{ $company }}</span>
                </a>

                <div class="max-w-lg">
                    <h2 class="text-3xl leading-tight font-extrabold tracking-tight text-balance xl:text-4xl">
                        {{ __('messages.hero_title', ['company' => $company]) }}
                    </h2>

                    <ul class="mt-8 grid gap-3 text-sm font-medium text-brand-100/90 sm:grid-cols-2">
                        @foreach ($highlights as $highlight)
                            <li class="inline-flex items-center gap-2.5">
                                <flux:icon.check-circle class="size-5 shrink-0 text-orange-400" />
                                {{ $highlight }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p class="text-xs text-brand-200/70">&copy; {{ date('Y') }} {{ $company }}</p>
            </aside>

            {{-- Form column --}}
            <div class="flex min-w-0 flex-col">

                {{-- Brand banner (phones and tablets): the same photo, kept short. --}}
                <header class="relative isolate overflow-hidden bg-brand-950 text-white lg:hidden">
                    <img src="{{ asset('images/photos/hero-port.jpg') }}" alt="" width="1920" height="1080" decoding="async" class="absolute inset-0 -z-10 size-full object-cover object-[70%_center]">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-950 via-brand-950/80 to-brand-950/40"></div>

                    <div class="mx-auto flex max-w-xl items-center justify-between gap-4 px-6 pt-5 pb-14">
                        <a href="{{ route('home') }}" class="inline-flex min-w-0 items-center gap-3" wire:navigate>
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white shadow-[0_8px_20px_-6px_var(--color-brand-500)]">
                                <x-app-logo-icon class="size-5" />
                            </span>
                            <span class="truncate text-base font-extrabold tracking-tight">{{ $company }}</span>
                        </a>
                        <x-public.locale-switcher tone="dark" />
                    </div>
                </header>

                <div class="hidden items-center justify-end px-8 pt-6 lg:flex">
                    <x-public.locale-switcher />
                </div>

                <main class="relative -mt-8 flex flex-1 flex-col rounded-t-[2rem] bg-white px-6 pt-10 pb-8 sm:px-10 lg:mt-0 lg:justify-center lg:rounded-none lg:px-16 lg:pt-0 dark:bg-zinc-950">
                    <div class="mx-auto w-full max-w-sm">
                        {{ $slot }}
                    </div>

                    <div class="mx-auto mt-10 w-full max-w-sm border-t border-zinc-200/80 pt-6 text-center text-sm dark:border-white/10">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-medium text-zinc-500 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" wire:navigate>
                            <flux:icon.arrow-left class="size-4" />
                            {{ __('admin.nav_public_site') }}
                        </a>
                    </div>
                </main>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
