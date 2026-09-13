@props(['value' => 5, 'size' => 'text-sm'])

@php
    $value = (int) round($value);
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5']) }}>
    @for ($i = 1; $i <= 5; $i++)
        <span class="material-symbols-outlined {{ $size }} {{ $i <= $value ? 'text-secondary' : 'text-outline/50' }}" style="{{ $i <= $value ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
    @endfor
</div>