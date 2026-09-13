@props(['label' => null, 'name' => null, 'type' => 'text', 'value' => '', 'placeholder' => null, 'required' => false, 'prefix' => null, 'error' => null])

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $name }}" class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $label }}</label>
    @endif
    <div class="relative flex items-center">
        @if($prefix)
            <span class="absolute left-3.5 font-label-stat text-label-stat text-outline pointer-events-none select-none">{{ $prefix }}</span>
        @endif
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full px-4 ' . ($prefix ? 'pl-14' : '') . ' py-3 rounded-xl bg-surface-container-low border border-outline-variant/30 text-on-surface font-body-sm text-body-sm placeholder:text-outline focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all']) }}>
    </div>
    @if($error)
        <p class="font-label-stat text-label-stat text-error">{{ $error }}</p>
    @endif
</div>