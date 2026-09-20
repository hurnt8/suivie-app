@php
    $status = $shipment->current_status;
    $steps = \App\Enums\ShipmentStatus::timelineSteps();
    $currentIndex = array_search($status, $steps, true);
    $isException = $currentIndex === false; // pending / delayed / returned / cancelled
    $isDelivered = $status === \App\Enums\ShipmentStatus::Delivered;

    $dot = match ($status->color()) {
        'emerald' => 'bg-emerald-500',
        'amber' => 'bg-amber-500',
        'sky' => 'bg-sky-500',
        'orange' => 'bg-orange-500',
        'red' => 'bg-red-500',
        default => 'bg-zinc-400',
    };

    $tone = match ($status->color()) {
        'orange' => 'bg-orange-100 text-orange-600 dark:bg-orange-500/15 dark:text-orange-400',
        'red' => 'bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-400',
        'sky' => 'bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400',
        default => 'bg-zinc-100 text-zinc-600 dark:bg-white/10 dark:text-zinc-300',
    };
@endphp

<x-layouts::public :title="__('messages.tracking_title', ['code' => $shipment->tracking_code])" :settings="$settings">

    <div class="bg-zinc-50/70 dark:bg-zinc-950">

        {{-- Header band --}}
        <section class="relative isolate overflow-hidden bg-brand-950 pt-10 pb-28 text-white sm:pt-14">
            <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-[#241a63] to-brand-900"></div>
                <div class="absolute -top-40 -right-40 size-[34rem] rounded-full border border-white/10"></div>
                <div class="absolute -top-16 -right-16 size-[24rem] rounded-full border border-white/10"></div>
                <div class="absolute inset-0 [background-image:radial-gradient(rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:26px_26px]"></div>
                <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-orange-500 via-orange-400 to-transparent"></div>
            </div>

            <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <x-public.art.parcels class="pointer-events-none absolute -top-12 right-6 hidden h-28 w-auto opacity-90 lg:block" />

                <a href="{{ route('home') }}#track" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-100/80 transition-colors hover:text-white">
                    <flux:icon.arrow-left class="size-4" />
                    {{ __('messages.nav_track') }}
                </a>

                <div class="mt-6 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-widest text-brand-200/80 uppercase">{{ __('messages.tracking_number') }}</p>
                        <h1 class="mt-2 font-mono text-3xl font-extrabold tracking-wider break-all sm:text-4xl">{{ $shipment->tracking_code }}</h1>
                        <p class="mt-2 text-sm text-brand-100/70">
                            {{ __('messages.tracking_created_on', ['date' => $shipment->created_at->translatedFormat('d M Y')]) }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="rounded-2xl bg-white/10 px-5 py-3 ring-1 ring-white/15 backdrop-blur">
                            <p class="text-[11px] font-semibold tracking-wider text-brand-200/80 uppercase">{{ __('messages.estimated_delivery') }}</p>
                            <p class="mt-0.5 text-base font-bold">{{ $shipment->estimated_delivery_date?->translatedFormat('d M Y') ?? __('messages.not_available') }}</p>
                        </div>

                        <div class="rounded-2xl bg-white px-5 py-3 text-zinc-900 shadow-lg">
                            <p class="text-[11px] font-semibold tracking-wider text-zinc-400 uppercase">{{ __('site.current_status') }}</p>
                            <p class="mt-0.5 inline-flex items-center gap-2 text-base font-bold">
                                <span class="size-2.5 rounded-full {{ $dot }}"></span>
                                {{ $status->label() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="relative z-10 -mt-14 pb-20">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                {{-- Progress stepper / exception notice --}}
                @if ($isException)
                    <div class="card-elegant flex items-start gap-4 p-6">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $tone }}">
                            <flux:icon.information-circle class="size-6" />
                        </span>
                        <div>
                            <p class="text-base font-bold text-zinc-900 dark:text-white">{{ $status->label() }}</p>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $status->description() }}</p>
                        </div>
                    </div>
                @else
                    <div class="card-elegant overflow-x-auto p-6">
                        <ol class="flex min-w-[640px] items-start">
                            @foreach ($steps as $index => $step)
                                @php
                                    $isDone = $index < $currentIndex || ($index === $currentIndex && $isDelivered);
                                    $isCurrent = $index === $currentIndex && ! $isDelivered;
                                @endphp
                                <li class="flex flex-1 items-start last:flex-none">
                                    <div class="flex w-20 flex-col items-center gap-2 text-center">
                                        <span @class([
                                            'flex size-9 items-center justify-center rounded-full text-xs font-bold',
                                            'bg-brand-600 text-white' => $isDone,
                                            'bg-orange-500 text-white ring-4 ring-orange-500/25' => $isCurrent,
                                            'bg-zinc-100 text-zinc-400 dark:bg-white/10' => ! $isDone && ! $isCurrent,
                                        ])>
                                            @if ($isDone)
                                                <flux:icon.check class="size-4" />
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </span>
                                        <span @class([
                                            'text-[11px] leading-tight font-medium',
                                            'text-zinc-900 dark:text-white' => $isDone || $isCurrent,
                                            'text-zinc-500 dark:text-zinc-400' => ! $isDone && ! $isCurrent,
                                        ])>
                                            {{ $step->label() }}
                                        </span>
                                    </div>
                                    @if (! $loop->last)
                                        <span @class([
                                            'mt-[17px] h-0.5 flex-1',
                                            'bg-brand-600' => $index < $currentIndex,
                                            'bg-zinc-200 dark:bg-white/10' => $index >= $currentIndex,
                                        ])></span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
                    {{-- Details --}}
                    <div class="space-y-6 lg:col-span-1">
                        <div class="card-elegant p-6">
                            <h2 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('messages.tracking_route') }}</h2>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-xs text-zinc-400">{{ __('messages.origin') }}</p>
                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->origin }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-400">{{ __('messages.destination') }}</p>
                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->destination }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-400">{{ __('messages.estimated_delivery') }}</p>
                                    <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                        {{ $shipment->estimated_delivery_date?->translatedFormat('d M Y') ?? __('messages.not_available') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card-elegant p-6">
                            <h2 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('messages.tracking_sender') }}</h2>
                            <p class="mt-3 text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->sender->maskedName() }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $shipment->sender->maskedEmail() }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $shipment->sender->city }}, {{ $shipment->sender->country }}</p>
                        </div>

                        <div class="card-elegant p-6">
                            <h2 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('messages.tracking_recipient') }}</h2>
                            <p class="mt-3 text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->recipient->maskedName() }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $shipment->recipient->maskedEmail() }}</p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $shipment->recipient->city }}, {{ $shipment->recipient->country }}</p>
                        </div>

                        @if ($shipment->hasAmount())
                            <div class="relative overflow-hidden rounded-2xl bg-brand-950 p-6 text-white shadow-[var(--shadow-elegant)]">
                                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-orange-500 to-orange-300"></span>
                                <p class="text-[11px] font-semibold tracking-widest text-brand-200/80 uppercase">{{ __('site.amount') }}</p>
                                <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ $shipment->formattedAmount() }}</p>
                            </div>
                        @endif

                        <div class="card-elegant p-6">
                            <h2 class="text-sm font-bold text-zinc-900 dark:text-white">{{ __('messages.tracking_package') }}</h2>
                            <dl class="mt-3 space-y-2 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-zinc-400">{{ __('messages.description') }}</dt><dd class="text-right font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->description ?? '—' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-400">{{ __('messages.weight') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->weight ? $shipment->weight.' kg' : '—' }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-400">{{ __('messages.package_count') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->package_count }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-400">{{ __('messages.service_type') }}</dt><dd class="font-medium text-zinc-800 dark:text-zinc-100">{{ $shipment->service_type->label() }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="card-elegant p-6 lg:col-span-2">
                        <h2 class="mb-6 text-sm font-bold text-zinc-900 dark:text-white">{{ __('messages.tracking_history') }}</h2>

                        @if ($shipment->events->isEmpty())
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('messages.tracking_no_events') }}</p>
                        @else
                            <x-tracking-timeline :events="$shipment->events->sortByDesc('event_date')" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layouts::public>
