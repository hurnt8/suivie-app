{{-- Hero illustration: a dotted globe with a live route, a delivery truck and floating parcels. Original artwork, decorative only. --}}
<svg viewBox="0 0 560 480" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" {{ $attributes }}>
    <defs>
        <radialGradient id="hs-globe" cx="36%" cy="30%" r="85%">
            <stop offset="0" stop-color="#4f46e5" />
            <stop offset=".55" stop-color="#312e81" />
            <stop offset="1" stop-color="#1e1b4b" />
        </radialGradient>
        <radialGradient id="hs-halo" cx="50%" cy="50%" r="50%">
            <stop offset="0" stop-color="#6366f1" stop-opacity=".35" />
            <stop offset="1" stop-color="#6366f1" stop-opacity="0" />
        </radialGradient>
        <linearGradient id="hs-beam" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fde68a" stop-opacity=".55" />
            <stop offset="1" stop-color="#fde68a" stop-opacity="0" />
        </linearGradient>
        <pattern id="hs-dots" width="12" height="12" patternUnits="userSpaceOnUse">
            <circle cx="3" cy="3" r="1.6" fill="#c7d2fe" />
        </pattern>
        <clipPath id="hs-clip"><circle cx="290" cy="212" r="168" /></clipPath>
        <mask id="hs-land">
            <rect width="560" height="480" fill="#000" />
            <g fill="#fff">
                <path d="M168 150 C188 120 236 116 252 140 C264 160 242 182 248 208 C254 236 234 266 216 286 C200 302 178 284 182 252 C186 226 158 196 168 150 Z" />
                <path d="M292 126 C314 114 348 122 354 146 C360 168 336 176 338 200 C340 228 354 252 338 284 C322 314 294 302 290 270 C286 240 298 218 284 198 C272 180 270 142 292 126 Z" />
                <path d="M374 150 C394 136 426 150 432 176 C438 200 414 214 398 214 C378 214 362 188 374 150 Z" />
                <path d="M394 262 C412 254 430 264 426 280 C422 294 398 296 392 282 Z" />
            </g>
        </mask>
    </defs>

    {{-- Halo + globe --}}
    <circle cx="290" cy="212" r="235" fill="url(#hs-halo)" />
    <circle cx="290" cy="212" r="168" fill="url(#hs-globe)" />
    <g clip-path="url(#hs-clip)">
        <rect x="110" y="40" width="360" height="360" fill="url(#hs-dots)" mask="url(#hs-land)" fill-opacity=".85" />
        <g stroke="#a5b4fc" stroke-opacity=".16" stroke-width="1.2">
            <ellipse cx="290" cy="212" rx="70" ry="168" />
            <ellipse cx="290" cy="212" rx="128" ry="168" />
            <path d="M122 212 H458" />
            <path d="M138 140 Q290 176 442 140" />
            <path d="M138 284 Q290 248 442 284" />
        </g>
    </g>
    <circle cx="290" cy="212" r="168" stroke="#a5b4fc" stroke-opacity=".3" stroke-width="1.5" />
    <circle cx="290" cy="212" r="196" stroke="#fff" stroke-opacity=".08" stroke-dasharray="3 9" stroke-linecap="round" />

    {{-- Route --}}
    <path d="M200 236 C240 60 380 60 398 190" stroke="#fff" stroke-opacity=".14" stroke-width="8" stroke-linecap="round" />
    <path class="art-dash" d="M200 236 C240 60 380 60 398 190" stroke="#fb923c" stroke-width="3.5" stroke-linecap="round" stroke-dasharray="6 10" />

    {{-- Pins --}}
    <g transform="translate(200 236)">
        <circle class="art-pulse" r="12" fill="#fb923c" fill-opacity=".5" />
        <path d="M0 0 C-6 -8 -16 -16 -16 -28 A16 16 0 1 1 16 -28 C16 -16 6 -8 0 0 Z" fill="#f97316" />
        <circle cy="-28" r="6" fill="#fff" />
    </g>
    <g transform="translate(398 192)">
        <circle class="art-pulse" r="12" fill="#818cf8" fill-opacity=".6" />
        <path d="M0 0 C-6 -8 -16 -16 -16 -28 A16 16 0 1 1 16 -28 C16 -16 6 -8 0 0 Z" fill="#fff" />
        <path d="M-6 -28 L-2 -23.5 L7 -33" stroke="#4f46e5" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" />
    </g>

    {{-- Plane on the route --}}
    <g transform="translate(307 98) rotate(-7)">
        <path d="M26 0 L-22 -16 L-12 0 L-22 16 Z" fill="#fff" />
        <path d="M-12 0 L26 0" stroke="#c7d2fe" stroke-width="2" stroke-linecap="round" />
    </g>

    {{-- Floating parcels --}}
    <g class="art-float" opacity=".95">
        <x-public.art.box :x="58" :y="58" :scale=".62" tone="indigo" />
    </g>
    <g class="art-float-slow">
        <x-public.art.box :x="478" :y="96" :scale=".46" />
    </g>

    {{-- Sparkles --}}
    <g stroke="#e0e7ff" stroke-opacity=".6" stroke-width="2" stroke-linecap="round">
        <path d="M136 28 V44 M128 36 H144" />
        <path d="M506 236 V248 M500 242 H512" />
        <path d="M34 214 V226 M28 220 H40" />
    </g>
    <circle cx="470" cy="60" r="3" fill="#fdba74" />
    <circle cx="96" cy="176" r="2.5" fill="#a5b4fc" />
    <circle cx="520" cy="182" r="2" fill="#a5b4fc" />

    {{-- Road --}}
    <rect x="14" y="446" width="532" height="8" rx="4" fill="#312e81" />
    <path class="art-dash" d="M20 450 H540" stroke="#a5b4fc" stroke-opacity=".55" stroke-width="2" stroke-dasharray="14 12" />

    {{-- Delivery truck --}}
    <g>
        <path d="M232 408 L300 392 V432 L232 418 Z" fill="url(#hs-beam)" />
        <path d="M14 400 h26 M4 414 h34 M20 428 h20" stroke="#fff" stroke-opacity=".28" stroke-width="3" stroke-linecap="round" />
        <rect x="44" y="336" width="118" height="84" rx="9" fill="#eef2ff" />
        <rect x="44" y="396" width="118" height="10" fill="#f97316" />
        <path d="M78 358 l14 10 -14 10 M96 358 l14 10 -14 10" stroke="#4f46e5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
        <rect x="44" y="418" width="188" height="9" rx="3" fill="#312e81" />
        <path d="M164 364 H198 Q206 364 211 371 L230 398 Q234 404 234 410 V420 H164 Z" fill="#f97316" />
        <path d="M174 374 H196 Q200 374 202 377 L216 398 H174 Z" fill="#c7d2fe" />
        <circle cx="230" cy="410" r="3.5" fill="#fde68a" />
        <g>
            <circle cx="82" cy="432" r="15" fill="#0f0d2e" stroke="#6366f1" stroke-width="2" />
            <circle cx="82" cy="432" r="6" fill="#c7d2fe" />
            <circle cx="198" cy="432" r="15" fill="#0f0d2e" stroke="#6366f1" stroke-width="2" />
            <circle cx="198" cy="432" r="6" fill="#c7d2fe" />
        </g>
    </g>

    {{-- Parcel on the road --}}
    <x-public.art.box :x="404" :y="322" :scale="1.12" />
</svg>
