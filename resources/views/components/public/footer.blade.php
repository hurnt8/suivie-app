@props(['settings'])

@php
    $company = $settings->company_name ?? config('app.name');
    $linkClass = 'text-zinc-400 transition-colors hover:text-white';
@endphp

<footer class="border-t border-white/10 bg-zinc-950 text-zinc-400">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5" wire:navigate>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-white">
                        <x-app-logo-icon class="size-5" />
                    </span>
                    <span class="text-lg font-extrabold tracking-tight text-white">{{ $company }}</span>
                </a>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">
                    {{ __('messages.footer_tagline') }}
                </p>
            </div>

            <nav class="lg:col-span-2 lg:col-start-6" aria-label="{{ __('messages.footer_navigation') }}">
                <h3 class="text-sm font-semibold text-white">{{ __('messages.footer_navigation') }}</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="{{ $linkClass }}">{{ __('messages.nav_home') }}</a></li>
                    <li><a href="{{ route('home') }}#track" class="{{ $linkClass }}">{{ __('messages.nav_track') }}</a></li>
                    <li><a href="{{ route('home') }}#how-it-works" class="{{ $linkClass }}">{{ __('messages.nav_how_it_works') }}</a></li>
                    <li><a href="{{ route('login') }}" class="{{ $linkClass }}">{{ __('messages.nav_login') }}</a></li>
                </ul>
            </nav>

            <div class="lg:col-span-2">
                <h3 class="text-sm font-semibold text-white">{{ __('site.footer_services') }}</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach (\App\Enums\ServiceType::cases() as $service)
                        <li><a href="{{ route('home') }}#services" class="{{ $linkClass }}">{{ $service->label() }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div id="contact" class="scroll-mt-24 lg:col-span-3">
                <h3 class="text-sm font-semibold text-white">{{ __('messages.footer_contact') }}</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @if (filled($settings->address ?? null))
                        <li class="flex gap-2.5"><flux:icon.map-pin class="mt-0.5 size-4 shrink-0 text-orange-400" /><span>{{ $settings->address }}</span></li>
                    @endif
                    @if (filled($settings->phone ?? null))
                        <li class="flex gap-2.5"><flux:icon.phone class="mt-0.5 size-4 shrink-0 text-orange-400" /><a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->phone) }}" class="{{ $linkClass }}">{{ $settings->phone }}</a></li>
                    @endif
                    @if (filled($settings->email ?? null))
                        <li class="flex gap-2.5"><flux:icon.envelope class="mt-0.5 size-4 shrink-0 text-orange-400" /><a href="mailto:{{ $settings->email }}" class="{{ $linkClass }}">{{ $settings->email }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col-reverse items-start justify-between gap-6 border-t border-white/10 pt-8 text-xs sm:flex-row sm:items-center">
            <p>&copy; {{ now()->year }} {{ $company }}. {{ __('messages.footer_rights') }}</p>
            <div class="flex items-center gap-4">
                <span>{{ __('messages.footer_demo_notice') }}</span>
                <x-public.locale-switcher tone="dark" />
            </div>
        </div>
    </div>
</footer>
