@props(['game' => null, 'count' => null])

<a href="{{ route('marketplace.index', ['game' => $game?->slug]) }}" class="group rounded-2xl bg-surface-container-low border border-outline-variant/20 p-4 flex flex-col gap-3 hover:border-secondary/50 hover:-translate-y-1 hover:shadow-[0_12px_40px_-12px_rgba(0,0,0,0.6)] transition-all duration-300 relative overflow-hidden">
    <div class="absolute top-6 right-4 w-24 h-24 rounded-full opacity-20 group-hover:opacity-40 transition-opacity" style="background-color: {{ $game?->icon_color ?? '#d0bcff' }}"></div>
    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-headline-sm text-headline-sm font-bold text-on-surface relative z-10 overflow-hidden"
         style="background-color: {{ $game?->icon_color ?? '#d0bcff' }}20; color: {{ $game?->icon_color ?? '#d0bcff' }}">
        @if($game?->iconUrl())
            <img src="{{ $game->iconUrl() }}" alt="{{ $game->name }}" class="w-full h-full object-cover">
        @else
            {{ str($game?->name ?? 'GC')->upper()->substr(0, 2) }}
        @endif
    </div>
    <div class="relative z-10 flex flex-col gap-1">
        <span class="font-body-md text-body-md font-semibold text-on-surface group-hover:text-secondary transition-colors">{{ $game?->name }}</span>
        <span class="font-label-stat text-label-stat text-on-surface-variant">{{ $count ? number_format($count, 0, ',', '.') . ' listing aktif' : 'Telusuri Listing' }}</span>
    </div>
</a>