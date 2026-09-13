@props(['game' => null, 'size' => 'w-11 h-11', 'icon' => 'text-xl'])

<div {{ $attributes->merge(['class' => $size . ' rounded-xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0 overflow-hidden']) }}>
    @if($game?->iconUrl())
        <img src="{{ $game->iconUrl() }}" alt="{{ $game->name }}" class="w-full h-full object-cover">
    @else
        <span class="material-symbols-outlined {{ $icon }}">sports_esports</span>
    @endif
</div>