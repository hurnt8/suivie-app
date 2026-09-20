{{-- A small stack of parcels with a paper plane, used as a backdrop on the call-to-action band. Original artwork. --}}
<svg viewBox="0 0 360 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" {{ $attributes }}>
    <ellipse cx="190" cy="214" rx="140" ry="12" fill="#0f0d2e" fill-opacity=".45" />
    <path class="art-dash" d="M20 130 C90 40 200 24 300 60" stroke="#fdba74" stroke-opacity=".8" stroke-width="3" stroke-linecap="round" stroke-dasharray="6 10" />
    <g transform="translate(300 60) rotate(28)">
        <path d="M22 0 L-18 -13 L-10 0 L-18 13 Z" fill="#fff" />
    </g>
    <x-public.art.box :x="70" :y="104" :scale="1.05" tone="indigo" />
    <x-public.art.box :x="178" :y="104" :scale="1.05" />
    <g class="art-float">
        <x-public.art.box :x="124" :y="40" :scale="1.05" />
    </g>
    <g stroke="#e0e7ff" stroke-opacity=".55" stroke-width="2" stroke-linecap="round">
        <path d="M322 156 v12 M316 162 h12" />
        <path d="M40 60 v10 M35 65 h10" />
    </g>
</svg>
