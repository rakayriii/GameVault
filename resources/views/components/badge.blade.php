@props(['variant' => 'neutral', 'icon' => null, 'label' => null])

@php
    $classes = match ($variant) {
        'primary' => 'bg-primary-container/30 text-primary',
        'secondary' => 'bg-secondary-container/30 text-secondary',
        'tertiary' => 'bg-tertiary-container/30 text-tertiary',
        'error' => 'bg-error-container/30 text-error',
        'success' => 'bg-tertiary-container/30 text-tertiary',
        'warning' => 'bg-tertiary-container/30 text-tertiary',
        'outline' => 'bg-transparent border border-outline-variant/40 text-on-surface-variant',
        default => 'bg-surface-container-highest text-on-surface-variant',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2.5 py-1 rounded-lg font-label-stat text-label-stat font-semibold tracking-wide ' . $classes]) }}>
    @if($icon)
        <span class="material-symbols-outlined text-sm">{{ $icon }}</span>
    @endif
    {!! $label ?? $slot !!}
</span>