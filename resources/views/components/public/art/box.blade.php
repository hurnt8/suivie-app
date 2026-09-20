{{-- An isometric cardboard parcel (≈100×110 units at scale 1), drawn inside a parent <svg>. Original artwork. --}}
@props(['x' => 0, 'y' => 0, 'scale' => 1, 'tone' => 'orange'])

@php
    [$top, $left, $right, $tapeTop, $tapeSide] = match ($tone) {
        'indigo' => ['#818cf8', '#4f46e5', '#3730a3', '#e0e7ff', '#c7d2fe'],
        default => ['#fb923c', '#ea580c', '#c2410c', '#ffedd5', '#fed7aa'],
    };
@endphp

<g {{ $attributes }} transform="translate({{ $x }} {{ $y }}) scale({{ $scale }})">
    <path d="M50 0 L100 25 L50 50 L0 25 Z" fill="{{ $top }}" />
    <path d="M0 25 L50 50 L50 110 L0 85 Z" fill="{{ $left }}" />
    <path d="M50 50 L100 25 L100 85 L50 110 Z" fill="{{ $right }}" />
    <path d="M70 10 L80 15 L30 40 L20 35 Z" fill="{{ $tapeTop }}" />
    <path d="M20 35 L30 40 L30 100 L20 95 Z" fill="{{ $tapeSide }}" />
    <path d="M65 56.5 L87.5 45.25 L87.5 65.25 L65 76.5 Z" fill="#fff" fill-opacity=".92" />
    <path d="M69 63 L83 56 M69 69 L78 64.5" stroke="{{ $right }}" stroke-width="2" stroke-linecap="round" />
</g>
