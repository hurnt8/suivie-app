<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="relative min-h-screen overflow-hidden bg-white antialiased dark:bg-zinc-950">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute inset-0 [background-image:radial-gradient(theme(colors.zinc.300)_1px,transparent_1px)] [background-size:28px_28px] opacity-[0.3] dark:[background-image:radial-gradient(theme(colors.white/10%)_1px,transparent_1px)]"></div>
            <div class="absolute top-1/2 left-1/2 h-[36rem] w-[36rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-tr from-brand-200 via-brand-100 to-transparent opacity-50 blur-3xl dark:from-brand-900 dark:via-brand-950 dark:opacity-30"></div>
        </div>

        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="mb-1 flex size-11 items-center justify-center rounded-xl bg-brand-600 text-white shadow-[0_8px_20px_-6px_theme(colors.brand.600/60%)]">
                        <x-app-logo-icon class="size-6 fill-current" />
                    </span>
                    <span class="text-base font-bold text-zinc-900 dark:text-white">{{ config('app.name', 'Laravel') }}</span>
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
