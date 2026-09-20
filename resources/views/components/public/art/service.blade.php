{{-- One illustration per delivery service (standard | express | economy | international). Original artwork, decorative only. --}}
@props(['type' => 'standard'])

<svg viewBox="0 0 320 150" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" {{ $attributes }}>
    <defs>
        <pattern id="sv-{{ $type }}-dots" width="14" height="14" patternUnits="userSpaceOnUse">
            <circle cx="2" cy="2" r="1.2" fill="#c7d2fe" fill-opacity=".22" />
        </pattern>
        <radialGradient id="sv-{{ $type }}-glow" cx="50%" cy="50%" r="50%">
            <stop offset="0" stop-color="#6366f1" stop-opacity=".45" />
            <stop offset="1" stop-color="#6366f1" stop-opacity="0" />
        </radialGradient>
    </defs>

    <rect width="320" height="150" fill="url(#sv-{{ $type }}-dots)" />
    <ellipse cx="160" cy="84" rx="130" ry="70" fill="url(#sv-{{ $type }}-glow)" />

    @switch($type)
        @case('express')
            <path d="M24 56 H84 M8 76 H80 M34 96 H88" stroke="#fff" stroke-opacity=".4" stroke-width="4" stroke-linecap="round" />
            <g class="art-float">
                <x-public.art.box :x="104" :y="24" :scale=".98" />
            </g>
            <circle cx="222" cy="46" r="24" fill="#f97316" />
            <path d="M226 31 L212 50 H222 L218 64 L233 43 H223 Z" fill="#fff" />
            <circle cx="262" cy="106" r="17" stroke="#e0e7ff" stroke-opacity=".85" stroke-width="3.5" />
            <path d="M262 106 V95 M262 106 L269 110 M257 86 H267" stroke="#e0e7ff" stroke-opacity=".85" stroke-width="3" stroke-linecap="round" />
            <path d="M0 132 H320" stroke="#312e81" stroke-width="20" />
            @break

        @case('economy')
            <ellipse cx="156" cy="128" rx="92" ry="9" fill="#0f0d2e" fill-opacity=".45" />
            <rect x="70" y="116" width="172" height="10" rx="2" fill="#b45309" />
            <path d="M84 126 v6 M156 126 v6 M228 126 v6" stroke="#92400e" stroke-width="8" stroke-linecap="round" />
            <x-public.art.box :x="82" :y="50" :scale=".68" tone="indigo" />
            <x-public.art.box :x="140" :y="50" :scale=".68" />
            <x-public.art.box :x="112" :y="22" :scale=".68" />
            <g class="art-float-slow">
                <circle cx="256" cy="52" r="25" fill="#fbbf24" />
                <circle cx="256" cy="52" r="18.5" stroke="#f59e0b" stroke-width="2.5" />
                <path d="M256 41 V61 M247 54 L256 63 L265 54" stroke="#92400e" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            </g>
            <path d="M40 40 v12 M34 46 h12 M286 98 v10 M281 103 h10" stroke="#e0e7ff" stroke-opacity=".55" stroke-width="2" stroke-linecap="round" />
            @break

        @case('international')
            <ellipse cx="160" cy="76" rx="92" ry="26" transform="rotate(-16 160 76)" stroke="#fdba74" stroke-width="2.5" stroke-dasharray="5 8" class="art-dash" />
            <circle cx="160" cy="78" r="46" fill="#4338ca" />
            <circle cx="160" cy="78" r="46" fill="url(#sv-{{ $type }}-dots)" />
            <g fill="#818cf8" fill-opacity=".75">
                <path d="M130 62 C138 50 154 52 158 62 C162 72 148 76 150 86 C152 96 140 100 134 92 C128 84 124 72 130 62 Z" />
                <path d="M172 58 C182 50 196 58 194 70 C192 80 180 82 174 76 C168 70 166 64 172 58 Z" />
                <path d="M176 92 C184 88 192 94 188 102 C184 108 176 106 174 100 Z" />
            </g>
            <g stroke="#c7d2fe" stroke-opacity=".3" stroke-width="1.2">
                <ellipse cx="160" cy="78" rx="20" ry="46" />
                <path d="M114 78 H206" />
            </g>
            <circle cx="160" cy="78" r="46" stroke="#a5b4fc" stroke-opacity=".5" stroke-width="1.5" />
            <g transform="translate(228 46) rotate(-30)">
                <path d="M18 0 L-16 -11 L-9 0 L-16 11 Z" fill="#fff" />
            </g>
            <g transform="translate(112 118)">
                <path d="M0 0 C-4 -6 -11 -11 -11 -19 A11 11 0 1 1 11 -19 C11 -11 4 -6 0 0 Z" fill="#f97316" />
                <circle cy="-19" r="4" fill="#fff" />
            </g>
            <path d="M262 108 c0 -8 7 -12 14 -10 c2 -8 14 -8 17 0 c8 0 12 10 4 14 h-31 c-4 0 -4 -3 -4 -4 Z" fill="#fff" fill-opacity=".14" />
            <path d="M22 44 c0 -6 5 -9 10 -8 c2 -6 10 -6 12 0 c6 0 9 7 3 10 h-22 c-3 0 -3 -2 -3 -2 Z" fill="#fff" fill-opacity=".12" />
            @break

        @default
            {{-- standard: a delivery van passing a small neighbourhood --}}
            <g>
                <rect x="20" y="70" width="46" height="60" rx="3" fill="#3730a3" />
                <path d="M14 72 L43 48 L72 72 Z" fill="#6366f1" />
                <rect x="30" y="84" width="10" height="12" rx="1.5" fill="#fdba74" />
                <rect x="46" y="84" width="10" height="12" rx="1.5" fill="#fdba74" fill-opacity=".45" />
                <rect x="36" y="108" width="14" height="22" rx="2" fill="#1e1b4b" />
                <rect x="252" y="80" width="42" height="50" rx="3" fill="#4338ca" />
                <path d="M247 82 L273 60 L299 82 Z" fill="#818cf8" />
                <rect x="260" y="92" width="9" height="11" rx="1.5" fill="#fdba74" fill-opacity=".5" />
                <rect x="276" y="92" width="9" height="11" rx="1.5" fill="#fdba74" />
                <circle cx="238" cy="92" r="13" fill="#6366f1" fill-opacity=".8" />
                <rect x="236" y="96" width="4" height="34" fill="#3730a3" />
            </g>
            <g class="art-float-slow">
                <path d="M96 76 H160 Q172 76 179 84 L200 106 Q204 110 204 115 V124 H96 Z" fill="#f8fafc" />
                <path d="M170 84 H176 Q180 84 183 88 L194 104 H170 Z" fill="#818cf8" />
                <rect x="96" y="106" width="108" height="6" fill="#f97316" />
                <path d="M114 88 l9 7 -9 7 M128 88 l9 7 -9 7" stroke="#4f46e5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="200" cy="112" r="2.6" fill="#fde68a" />
                <circle cx="124" cy="128" r="11" fill="#0f0d2e" stroke="#6366f1" stroke-width="2" />
                <circle cx="124" cy="128" r="4.5" fill="#c7d2fe" />
                <circle cx="180" cy="128" r="11" fill="#0f0d2e" stroke="#6366f1" stroke-width="2" />
                <circle cx="180" cy="128" r="4.5" fill="#c7d2fe" />
                <path d="M62 92 H90 M52 104 H84 M70 116 H90" stroke="#fff" stroke-opacity=".3" stroke-width="3" stroke-linecap="round" />
            </g>
            <rect x="0" y="138" width="320" height="12" fill="#312e81" />
            <path class="art-dash" d="M0 144 H320" stroke="#a5b4fc" stroke-opacity=".55" stroke-width="2" stroke-dasharray="14 12" />
    @endswitch
</svg>
