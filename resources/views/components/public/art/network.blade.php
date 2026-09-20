{{-- A stylised city map with a delivery route between two pins. Original artwork, decorative only. --}}
<svg viewBox="0 0 560 420" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" {{ $attributes }}>
    <defs>
        <linearGradient id="nw-bg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#312e81" />
            <stop offset="1" stop-color="#1e1b4b" />
        </linearGradient>
    </defs>

    <rect width="560" height="420" fill="url(#nw-bg)" />

    {{-- River --}}
    <path d="M-20 330 C120 300 190 380 320 340 C440 304 480 250 600 240" stroke="#4f46e5" stroke-opacity=".45" stroke-width="30" stroke-linecap="round" />
    <path d="M-20 330 C120 300 190 380 320 340 C440 304 480 250 600 240" stroke="#818cf8" stroke-opacity=".35" stroke-width="2" stroke-dasharray="4 14" stroke-linecap="round" />

    {{-- City blocks --}}
    <g fill="#fff" fill-opacity=".06">
        <rect x="24" y="24" width="120" height="76" rx="10" />
        <rect x="164" y="24" width="150" height="76" rx="10" />
        <rect x="334" y="24" width="100" height="76" rx="10" />
        <rect x="454" y="24" width="84" height="76" rx="10" />
        <rect x="24" y="120" width="120" height="70" rx="10" />
        <rect x="164" y="120" width="70" height="70" rx="10" />
        <rect x="254" y="120" width="180" height="70" rx="10" />
        <rect x="454" y="120" width="84" height="70" rx="10" />
        <rect x="24" y="210" width="76" height="60" rx="10" />
        <rect x="120" y="210" width="114" height="60" rx="10" />
        <rect x="254" y="210" width="110" height="60" rx="10" />
        <rect x="384" y="210" width="154" height="46" rx="10" />
    </g>
    <g fill="#34d399" fill-opacity=".22">
        <rect x="164" y="120" width="70" height="70" rx="10" />
        <rect x="454" y="24" width="84" height="76" rx="10" />
    </g>
    <g fill="#c7d2fe" fill-opacity=".16">
        <rect x="40" y="40" width="26" height="18" rx="3" />
        <rect x="76" y="40" width="50" height="18" rx="3" />
        <rect x="180" y="40" width="42" height="18" rx="3" />
        <rect x="350" y="40" width="34" height="34" rx="3" />
        <rect x="270" y="136" width="60" height="20" rx="3" />
        <rect x="346" y="136" width="70" height="20" rx="3" />
        <rect x="136" y="226" width="40" height="24" rx="3" />
        <rect x="270" y="226" width="34" height="24" rx="3" />
    </g>

    {{-- Route --}}
    <path d="M92 346 V246 Q92 232 106 232 H236 Q252 232 252 216 V126 Q252 110 268 110 H440" stroke="#fff" stroke-opacity=".12" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" />
    <path class="art-dash" d="M92 346 V246 Q92 232 106 232 H236 Q252 232 252 216 V126 Q252 110 268 110 H440" stroke="#fb923c" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="6 10" />

    {{-- Completed stops --}}
    <g>
        <circle cx="92" cy="240" r="11" fill="#fff" />
        <path d="M86.5 240 L90.5 244 L98 236" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="252" cy="196" r="11" fill="#fff" />
        <path d="M246.5 196 L250.5 200 L258 192" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
    </g>

    {{-- Vehicle, currently moving --}}
    <g transform="translate(252 152)">
        <circle class="art-pulse" r="14" fill="#fb923c" fill-opacity=".5" />
        <circle r="12" fill="#f97316" stroke="#fff" stroke-width="3" />
    </g>

    {{-- Start: sender's warehouse --}}
    <g transform="translate(92 356)">
        <circle class="art-pulse" r="12" fill="#818cf8" fill-opacity=".6" />
        <path d="M0 0 C-6 -8 -16 -16 -16 -28 A16 16 0 1 1 16 -28 C16 -16 6 -8 0 0 Z" fill="#818cf8" />
        <path d="M-8 -26 L0 -34 L8 -26 V-20 H-8 Z" fill="#fff" />
    </g>

    {{-- End: recipient --}}
    <g transform="translate(440 110)">
        <circle class="art-pulse" r="12" fill="#fb923c" fill-opacity=".6" />
        <path d="M0 0 C-6 -8 -16 -16 -16 -28 A16 16 0 1 1 16 -28 C16 -16 6 -8 0 0 Z" fill="#f97316" />
        <circle cy="-28" r="6" fill="#fff" />
    </g>

    <g class="art-float">
        <x-public.art.box :x="452" :y="262" :scale=".62" />
    </g>
</svg>
