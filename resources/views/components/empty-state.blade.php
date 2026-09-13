@props(['icon' => 'inbox', 'title' => null, 'description' => null])

<div class="flex flex-col items-center justify-center gap-3 py-14 px-6 text-center">
    <span class="w-16 h-16 rounded-2xl bg-surface-container-high flex items-center justify-center">
        <span class="material-symbols-outlined text-3xl text-on-surface-variant">{{ $icon }}</span>
    </span>
    @if($title)
        <h3 class="font-body-lg text-body-lg font-semibold text-on-surface">{{ $title }}</h3>
    @endif
    @if($description)
        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-sm">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>