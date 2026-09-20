@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1.5 text-start">
    <h1 class="text-2xl font-extrabold tracking-tight text-zinc-900 sm:text-3xl dark:text-white">{{ $title }}</h1>
    <p class="text-sm leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
</div>
