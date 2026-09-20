@props(['tone' => 'default'])

@php
    $locales = [
        'fr' => 'Français',
        'en' => 'English',
        'es' => 'Español',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'ro' => 'Română',
        'pl' => 'Polski',
    ];
@endphp

<flux:dropdown position="bottom" align="end">
    <flux:button
        size="sm"
        variant="ghost"
        icon="globe-alt"
        icon-trailing="chevron-down"
        :class="$tone === 'dark' ? 'text-white! hover:bg-white/10!' : ''"
    >
        {{ strtoupper(app()->getLocale()) }}
    </flux:button>

    <flux:menu>
        @foreach ($locales as $code => $label)
            <flux:menu.item :href="route('locale.switch', $code)" :icon="app()->getLocale() === $code ? 'check' : null">
                {{ $label }}
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown>
