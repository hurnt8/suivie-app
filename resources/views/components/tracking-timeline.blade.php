@props(['events'])

<ol class="relative">
    @foreach ($events as $index => $event)
        @php $isLast = $loop->last; @endphp
        <li class="relative flex gap-4 pb-10 last:pb-0">
            @unless ($isLast)
                <span class="absolute top-8 left-[15px] h-[calc(100%-1rem)] w-px bg-gradient-to-b from-zinc-200 to-transparent dark:from-white/10"></span>
            @endunless

            <span @class([
                'relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full shadow-sm ring-4 ring-white dark:ring-zinc-900',
                'bg-emerald-500 shadow-emerald-500/30' => $event->status->color() === 'emerald',
                'bg-amber-500 shadow-amber-500/30' => $event->status->color() === 'amber',
                'bg-sky-500 shadow-sky-500/30' => $event->status->color() === 'sky',
                'bg-orange-500 shadow-orange-500/30' => $event->status->color() === 'orange',
                'bg-red-500 shadow-red-500/30' => $event->status->color() === 'red',
                'bg-zinc-400 shadow-zinc-400/30' => $event->status->color() === 'zinc',
                'ring-4 ring-offset-2 ring-offset-white dark:ring-offset-zinc-900' => $index === 0,
            ])>
                <flux:icon.check class="size-4 text-white" />
            </span>

            <div class="flex-1 pt-0.5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $event->displayTitle() }}</p>
                    <time class="text-xs font-medium text-zinc-400">{{ $event->event_date->translatedFormat('d M Y — H:i') }}</time>
                </div>
                @if ($event->displayDescription())
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ $event->displayDescription() }}</p>
                @endif
                @if ($event->location)
                    <p class="mt-1 flex items-center gap-1 text-xs font-medium text-zinc-400">
                        <flux:icon.map-pin class="size-3.5" />
                        {{ $event->location }}
                    </p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
