@php
    $company = $settings->company_name;

    $features = [
        __('messages.feature_global'),
        __('messages.feature_secure'),
        __('messages.feature_realtime'),
        __('messages.feature_support'),
    ];

    $tiles = [
        ['icon' => 'magnifying-glass', 'title' => __('messages.nav_track'), 'text' => __('site.quick_track_text'), 'href' => '#track', 'focus' => true],
        ['icon' => 'map', 'title' => __('messages.nav_how_it_works'), 'text' => __('site.quick_how_text'), 'href' => '#how-it-works'],
        ['icon' => 'chat-bubble-left-right', 'title' => __('site.nav_contact'), 'text' => __('site.quick_contact_text'), 'href' => '#contact'],
        ['icon' => 'user-circle', 'title' => __('site.quick_operator'), 'text' => __('site.quick_operator_text'), 'href' => route('login')],
    ];

    $serviceIcons = [
        'standard' => 'truck',
        'express' => 'bolt',
        'economy' => 'banknotes',
        'international' => 'globe-alt',
    ];

    $steps = [
        ['title' => __('messages.how_step_1_title'), 'text' => __('messages.how_step_1_text')],
        ['title' => __('messages.how_step_2_title'), 'text' => __('messages.how_step_2_text')],
        ['title' => __('messages.how_step_3_title'), 'text' => __('messages.how_step_3_text')],
    ];
@endphp

<x-layouts::public :title="__('messages.home_title')" :settings="$settings">

    {{-- Hero --}}
    <section class="relative isolate overflow-hidden bg-brand-950 text-white">
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <img src="{{ asset('images/photos/hero-port.jpg') }}" alt="" width="1920" height="1080" fetchpriority="high" decoding="async" class="absolute inset-0 size-full object-cover object-[75%_center]">
            <div class="absolute inset-0 bg-gradient-to-b from-brand-950/90 via-brand-950/80 to-brand-950/70 lg:bg-gradient-to-r lg:from-brand-950 lg:via-brand-950/80 lg:to-brand-950/10"></div>
            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-brand-950/60 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-orange-500 via-orange-400 to-transparent"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 pt-14 pb-32 sm:px-6 sm:pt-20 lg:px-8 lg:pt-24 lg:pb-40">
            <div class="lg:max-w-[58%]">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-brand-100 ring-1 ring-white/15">
                    <span class="size-1.5 rounded-full bg-orange-400"></span>
                    {{ __('messages.hero_badge') }}
                </span>

                <h1 class="mt-6 max-w-2xl text-4xl font-extrabold tracking-tight text-balance sm:text-5xl lg:text-6xl">
                    {{ __('messages.hero_title', ['company' => $company]) }}
                </h1>

                <p class="mt-5 max-w-xl text-lg leading-relaxed text-brand-100/80">
                    {{ __('messages.hero_subtitle') }}
                </p>

                {{-- Tracking card --}}
                <div id="track" class="mt-10 max-w-2xl scroll-mt-32 rounded-2xl bg-white p-5 text-zinc-900 shadow-2xl shadow-black/30 ring-1 ring-black/5 sm:p-6 dark:bg-zinc-900 dark:text-white dark:ring-white/10">
                    <div class="flex items-center gap-3">
                        <span class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                            <flux:icon.map-pin class="size-5" />
                        </span>
                        <h2 class="text-lg font-bold tracking-tight">{{ __('site.track_card_title') }}</h2>
                    </div>

                    <form method="GET" action="{{ route('tracking.search') }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <flux:input
                                id="tracking-code"
                                name="code"
                                :label="__('site.track_label')"
                                :placeholder="__('messages.track_placeholder')"
                                value="{{ old('code') }}"
                                autocomplete="off"
                                class="[&_input]:h-12 [&_input]:text-base"
                            />
                        </div>
                        <flux:button type="submit" variant="primary" color="orange" class="h-12! w-full px-8! text-base! sm:w-auto">
                            {{ __('messages.track_button') }}
                        </flux:button>
                    </form>

                    @error('code')
                        <p class="mt-3 text-sm font-medium text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                    @enderror

                    <a href="#how-it-works" class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-brand-700 hover:underline dark:text-brand-300">
                        <flux:icon.question-mark-circle class="size-4" />
                        {{ __('site.track_help') }}
                    </a>
                </div>

                <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-brand-100/90">
                    @foreach ($features as $feature)
                        <li class="inline-flex items-center gap-2">
                            <flux:icon.check-circle class="size-5 text-orange-400" />
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </section>

    {{-- Quick actions --}}
    <section class="relative z-10 -mt-16 sm:-mt-20" aria-labelledby="quick-title">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 id="quick-title" class="sr-only">{{ __('site.quick_title') }}</h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiles as $tile)
                    <a
                        href="{{ $tile['href'] }}"
                        @if ($tile['focus'] ?? false) x-data @click="setTimeout(() => document.getElementById('tracking-code')?.focus({ preventScroll: true }), 500)" @endif
                        class="group card-elegant flex flex-col p-6 transition-all hover:-translate-y-1 hover:shadow-[var(--shadow-elegant-lg)]"
                    >
                        <span class="flex size-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                            <flux:icon :icon="$tile['icon']" class="size-6" />
                        </span>
                        <h3 class="mt-5 text-base font-bold tracking-tight text-zinc-900 dark:text-white">{{ $tile['title'] }}</h3>
                        <p class="mt-1.5 flex-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $tile['text'] }}</p>
                        <span class="mt-5 inline-flex items-center text-brand-700 dark:text-brand-300">
                            <flux:icon.arrow-right class="size-5 transition-transform group-hover:translate-x-1" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section id="services" class="scroll-mt-24 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold tracking-widest text-orange-600 uppercase dark:text-orange-400">{{ $company }}</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">{{ __('site.services_title') }}</h2>
                <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('site.services_subtitle') }}</p>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (\App\Enums\ServiceType::cases() as $service)
                    <div class="group card-elegant flex flex-col overflow-hidden transition-all hover:-translate-y-1 hover:shadow-[var(--shadow-elegant-lg)]">
                        <div class="relative aspect-[16/10] overflow-hidden bg-brand-900">
                            <img
                                src="{{ asset('images/photos/service-'.$service->value.'.jpg') }}"
                                alt=""
                                width="900"
                                height="562"
                                loading="lazy"
                                decoding="async"
                                class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
                            >
                            <span class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-brand-500 to-orange-500"></span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center gap-3">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                                    <flux:icon :icon="$serviceIcons[$service->value]" class="size-5" />
                                </span>
                                <h3 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $service->label() }}</h3>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('site.service_'.$service->value.'_text') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="scroll-mt-24 bg-zinc-50 py-20 sm:py-28 dark:bg-white/[0.03]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold tracking-widest text-orange-600 uppercase dark:text-orange-400">{{ $company }}</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">{{ __('messages.how_it_works_title') }}</h2>
                <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('messages.how_it_works_subtitle') }}</p>
            </div>

            <ol class="relative mx-auto mt-16 grid max-w-5xl gap-12 md:grid-cols-3">
                <div class="absolute top-7 right-[16.6%] left-[16.6%] hidden border-t-2 border-dashed border-zinc-300 md:block dark:border-white/15" aria-hidden="true"></div>

                @foreach ($steps as $index => $step)
                    <li class="relative flex flex-col items-center text-center">
                        <span class="relative z-10 flex size-14 items-center justify-center rounded-full bg-brand-950 text-lg font-extrabold text-white ring-8 ring-zinc-50 dark:bg-brand-600 dark:ring-zinc-950">
                            {{ $index + 1 }}
                        </span>
                        <h3 class="mt-6 text-lg font-bold tracking-tight text-zinc-900 dark:text-white">{{ $step['title'] }}</h3>
                        <p class="mt-2 max-w-xs text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Network --}}
    <section id="network" class="scroll-mt-24 py-20 sm:py-28">
        <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
            <div class="relative pb-6">
                <div class="overflow-hidden rounded-3xl shadow-[var(--shadow-elegant-lg)] ring-1 ring-black/5 dark:ring-white/10">
                    <img src="{{ asset('images/photos/network-warehouse.jpg') }}" alt="" width="1200" height="800" loading="lazy" decoding="async" class="aspect-[4/3] w-full object-cover">
                </div>

                <div class="absolute bottom-0 left-4 flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-zinc-900 shadow-xl ring-1 ring-black/5 sm:left-8 dark:bg-zinc-800 dark:text-white dark:ring-white/10" aria-hidden="true">
                    <span class="flex size-9 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                        <flux:icon.truck class="size-5" />
                    </span>
                    <span class="text-sm font-semibold">{{ \App\Enums\ShipmentStatus::InTransit->label() }}</span>
                </div>

                <div class="absolute top-4 right-4 flex items-center gap-2 rounded-full bg-white/95 py-1.5 pr-3.5 pl-2 text-zinc-900 shadow-lg sm:top-6 sm:right-6" aria-hidden="true">
                    <span class="flex size-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <flux:icon.check class="size-4" />
                    </span>
                    <span class="text-xs font-semibold">{{ \App\Enums\ShipmentStatus::PickedUp->label() }}</span>
                </div>
            </div>

            <div>
                <span class="text-xs font-bold tracking-widest text-orange-600 uppercase dark:text-orange-400">{{ $company }}</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-balance text-zinc-900 sm:text-4xl dark:text-white">{{ __('site.network_title') }}</h2>
                <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-400">{{ __('site.network_text') }}</p>

                <ul class="mt-8 space-y-4">
                    @foreach (['network_point_1', 'network_point_2', 'network_point_3'] as $point)
                        <li class="flex items-start gap-3 text-sm font-medium text-zinc-700 dark:text-zinc-200">
                            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                                <flux:icon.check class="size-4" />
                            </span>
                            {{ __('site.'.$point) }}
                        </li>
                    @endforeach
                </ul>

                <flux:button
                    href="#track"
                    variant="primary"
                    class="mt-10 h-12! px-8! text-base!"
                    x-data
                    x-on:click="setTimeout(() => document.getElementById('tracking-code')?.focus({ preventScroll: true }), 500)"
                >
                    {{ __('messages.track_button') }}
                </flux:button>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative isolate overflow-hidden bg-brand-950 py-20 sm:py-28">
        <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
            <img src="{{ asset('images/photos/cta-port-night.jpg') }}" alt="" width="1600" height="760" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-brand-950 via-brand-950/85 to-brand-950/30"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ __('messages.cta_title') }}</h2>
                <p class="mt-2 max-w-xl text-brand-100/80">{{ __('messages.cta_subtitle') }}</p>
                <flux:button
                    href="#track"
                    variant="primary"
                    class="mt-8 h-12! px-8! text-base!"
                    x-data
                    x-on:click="setTimeout(() => document.getElementById('tracking-code')?.focus({ preventScroll: true }), 500)"
                >
                    {{ __('messages.cta_button') }}
                </flux:button>
            </div>
        </div>
    </section>

</x-layouts::public>
