@props(['label' => null, 'name' => null, 'value' => '', 'placeholder' => null, 'rows' => 4, 'required' => false, 'error' => null])

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $name }}" class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $label }}</label>
    @endif
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-xl bg-surface-container-low border border-outline-variant/30 text-on-surface font-body-sm text-body-sm placeholder:text-outline focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/20 transition-all resize-none']) }}>{{ $value }}</textarea>
    @if($error)
        <p class="font-label-stat text-label-stat text-error">{{ $error }}</p>
    @endif
</div>