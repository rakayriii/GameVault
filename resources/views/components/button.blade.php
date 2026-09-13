@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
    'icon' => null,
])

@php
    $variants = [
        'primary' => 'bg-primary-container text-on-primary-container font-semibold hover:bg-primary hover:text-on-primary shadow-[0_4px_16px_rgba(160,120,255,0.35)]',
        'gradient' => 'bg-gradient-to-r from-primary via-secondary to-tertiary text-on-primary font-bold hover:shadow-[0_8px_30px_-8px_rgba(180,130,255,0.8)]',
        'outline' => 'bg-transparent border border-outline-variant/40 text-on-surface hover:border-secondary/60 hover:text-secondary',
        'ghost' => 'bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high',
        'danger' => 'bg-error-container/30 text-error hover:bg-error-container/60',
    ];
    $base = 'inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl font-body-sm text-body-sm transition-all';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base . ' ' . $variants[$variant]]) }}>
        @if($icon)<span class="material-symbols-outlined text-base">{{ $icon }}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base . ' ' . $variants[$variant]]) }}>
        @if($icon)<span class="material-symbols-outlined text-base">{{ $icon }}</span>@endif
        {{ $slot }}
    </button>
@endif