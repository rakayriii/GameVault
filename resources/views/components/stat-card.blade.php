@props(['icon' => null, 'value' => null, 'label' => null, 'accent' => 'text-primary', 'iconBg' => 'bg-primary-container/30'])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-surface-container-lowest border border-outline-variant/20 p-5 flex flex-col gap-3']) }}>
    <div class="flex items-center justify-between">
        <span class="material-symbols-outlined text-2xl {{ $accent }}">{{ $icon ?? 'stat_0' }}</span>
        <span class="h-2 w-2 rounded-full {{ $accent }} opacity-30"></span>
    </div>
    <div class="flex flex-col gap-1">
        <span class="font-mono font-bold text-xl text-on-surface leading-none">{{ $value }}</span>
        <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">{{ $label }}</span>
    </div>
</div>