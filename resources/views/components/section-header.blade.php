@props(['title' => null, 'subtitle' => null, 'actionLabel' => null, 'actionUrl' => null])

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div class="flex flex-col gap-1.5">
        @if($title)
            <h2 class="font-headline-sm text-headline-xl text-on-surface tracking-tight font-bold">{{ $title }}</h2>
        @endif
        @if($subtitle)
            <p class="font-body-sm text-body-sm text-on-surface-variant max-w-xl">{{ $subtitle }}</p>
        @endif
    </div>
    @if($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high font-body-sm text-body-sm font-semibold transition-all shrink-0">
            {{ $actionLabel }}
            <span class="material-symbols-outlined text-base">arrow_forward</span>
        </a>
    @endif
</div>