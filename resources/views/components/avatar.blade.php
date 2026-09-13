@props(['user' => null, 'name' => null, 'color' => null, 'size' => 'w-9 h-9 text-sm'])

@php
    $name = $name ?? ($user ? $user->username : 'U');
    $colors = [
        'primary' => 'bg-primary-container/30 text-primary',
        'secondary' => 'bg-secondary-container/30 text-secondary',
        'tertiary' => 'bg-tertiary-container/30 text-tertiary',
        'neutral' => 'bg-surface-container-high text-on-surface',
    ];
    $palette = $color ?: ['primary', 'secondary', 'tertiary', 'neutral'][abs(crc32($name)) % 4];
    $circle = $colors[$palette] ?? $colors['primary'];
    $initials = strtoupper(collect(explode(' ', preg_replace('/[^A-Za-z0-9 ]/', '', $name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('')) ?: 'U';
@endphp

<div {{ $attributes->merge(['class' => $circle . ' ' . $size . ' rounded-full flex items-center justify-center font-bold font-label-stat shrink-0']) }}>
    <span class="font-label-stat font-bold">{{ $initials }}</span>
</div>