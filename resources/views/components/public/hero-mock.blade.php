{{-- Decorative preview of a tracking card. Sample data only — hidden from assistive tech. --}}
@php
    $done = 3;
    $eta = now()->addDays(2)->translatedFormat('d M Y');
@endphp

<div aria-hidden="true" class="relative mx-auto w-full max-w-md select-none">
    <div class="absolute -inset-6 rounded-[2.5rem] bg-gradient-to-br from-orange-500/25 to-brand-400/20 blur-3xl"></div>

    <div class="relative rotate-[1.5deg] rounded-3xl bg-white p-6 text-zinc-900 shadow-2xl shadow-black/40 ring-1 ring-black/5 dark:bg-zinc-900 dark:text-white dark:ring-white/10">
        <div class="flex items-center justify-between gap-3">
            <span class="text-[11px] font-semibold tracking-widest text-zinc-400 uppercase">{{ __('site.preview_label') }}</span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                <span class="size-1.5 rounded-full bg-amber-500"></span>
                {{ \App\Enums\ShipmentStatus::InTransit->label() }}
            </span>
        </div>

        <p class="mt-3 font-mono text-xl font-bold tracking-wider">LVR-2026-8X92K7</p>

        <div class="mt-6 flex items-center gap-3">
            <span class="text-sm font-semibold">Paris</span>
            <div class="relative flex-1">
                <div class="border-t-2 border-dashed border-zinc-300 dark:border-white/20"></div>
                <span class="absolute top-1/2 left-[60%] flex size-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-brand-600 text-white shadow-lg shadow-brand-600/40">
                    <flux:icon.truck class="size-4" />
                </span>
            </div>
            <span class="text-sm font-semibold">Lyon</span>
        </div>

        <div class="mt-6 flex gap-1.5">
            @for ($i = 1; $i <= 5; $i++)
                <span @class([
                    'h-1.5 flex-1 rounded-full',
                    'bg-brand-600' => $i <= $done,
                    'bg-orange-500' => $i === $done + 1,
                    'bg-zinc-200 dark:bg-white/10' => $i > $done + 1,
                ])></span>
            @endfor
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 border-t border-zinc-100 pt-4 dark:border-white/10">
            <div>
                <p class="text-[11px] text-zinc-400">{{ __('messages.estimated_delivery') }}</p>
                <p class="mt-0.5 text-sm font-semibold">{{ $eta }}</p>
            </div>
            <div>
                <p class="text-[11px] text-zinc-400">{{ __('site.current_status') }}</p>
                <p class="mt-0.5 text-sm font-semibold">{{ \App\Enums\ShipmentStatus::InTransit->label() }}</p>
            </div>
        </div>
    </div>

    <div class="absolute -bottom-10 -left-8 flex items-center gap-3 rounded-2xl bg-white px-4 py-3 text-zinc-900 shadow-xl ring-1 ring-black/5 dark:bg-zinc-800 dark:text-white dark:ring-white/10">
        <span class="flex size-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
            <flux:icon.check class="size-5" />
        </span>
        <span class="text-sm font-semibold">{{ \App\Enums\ShipmentStatus::PickedUp->label() }}</span>
    </div>
</div>
