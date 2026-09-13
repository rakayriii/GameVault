@props(['label' => null, 'value' => null, 'color' => 'text-on-surface', 'hint' => null])

<div class="p-4 rounded-xl bg-surface-container flex flex-col justify-between">
    <span class="font-label-stat text-label-stat text-outline uppercase">{{ $label }}</span>
    <span class="font-headline-lg text-headline-lg {{ $color }} font-bold font-label-mono mt-1">{{ $value }}</span>
    <span class="font-label-stat text-[10px] text-on-surface-variant mt-1">{{ $hint }}</span>
</div>