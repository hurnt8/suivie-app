<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="relative isolate min-h-screen overflow-hidden bg-brand-950 antialiased">
        {{-- Same deep-indigo backdrop as the public hero, so login feels like part of the site. --}}
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-[#241a63] to-brand-900"></div>
            <div class="absolute -top-44 -right-44 size-[40rem] rounded-full border border-white/10"></div>
            <div class="absolute -bottom-52 -left-52 size-[36rem] rounded-full border border-white/10"></div>
            <div class="absolute top-1/3 -right-10 size-72 rounded-full bg-orange-500/15 blur-3xl"></div>
            <div class="absolute inset-0 [background-image:radial-gradient(rgba(255,255,255,0.09)_1px,transparent_1px)] [background-size:28px_28px]"></div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-orange-500 via-orange-400 to-transparent"></div>
        </div>

        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="mb-1 flex size-11 items-center justify-center rounded-xl bg-brand-600 text-white shadow-[0_8px_20px_-6px_var(--color-brand-500)]">
                        <x-app-logo-icon class="size-6" />
                    </span>
                    <span class="text-base font-extrabold tracking-tight text-white">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="card-elegant-lg mt-4 flex flex-col gap-6 p-8">
                    {{ $slot }}
                </div>
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
