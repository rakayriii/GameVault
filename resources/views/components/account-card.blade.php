@props(['account' => null, 'layout' => 'grid', 'wishlisted' => false, 'mediaClass' => 'aspect-[16/10]'])

@php
    $game = $account->game ?? null;
    $price = $account->price;
    $strike = $account->strike_price ?? null;
    $discount = $account->discount_percent ?? ($strike && $strike > $price ? (int) round((($strike - $price) / $strike) * 100) : null);
    $final = $price;
    $rank = $account->rank ?: $game->name ?? '';
    $mediaClasses = $layout === 'row'
        ? 'h-full w-full lg:w-[320px] min-h-[240px] relative overflow-hidden'
        : 'relative overflow-hidden ' . $mediaClass;
    $bodyClasses = $layout === 'row' ? 'flex-1 p-5 flex flex-col gap-3' : 'p-4 flex flex-col gap-2.5';
@endphp

<div class="w-full group rounded-2xl overflow-hidden bg-surface-container-low border border-outline-variant/20 hover:border-secondary/50 hover:shadow-[0_12px_40px_-12px_rgba(0,0,0,0.6)] transition-all duration-300 {{ $layout === 'row' ? 'flex flex-col lg:flex-row' : '' }}">
    <a href="{{ route('marketplace.show', $account) }}" class="{{ $mediaClasses }}">
        @if($account->images->first())
            <img src="{{ $account->images->first()->url() }}" alt="{{ $account->title }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-surface-container-high via-surface-container to-surface-container-lowest"></div>
            <span class="absolute top-5 left-5 text-6xl font-extrabold font-headline-sm text-on-surface/15 select-none tracking-tighter">{{ substr($game?->name ?? 'GV', 0, 2) }}</span>
        @endif
        @if($discount)
            <span class="absolute top-3 right-3 z-10 flex items-center gap-1 rounded-lg bg-tertiary text-on-tertiary px-1.5 py-1 font-label-stat text-label-stat font-bold shadow-lg shadow-tertiary/10">
                Drop {{ $discount }}%
            </span>
        @endif
        <span class="absolute bottom-3 left-3 z-10 inline-flex items-center gap-1 rounded-lg bg-background/70 backdrop-blur-md border border-white/5 px-2 py-1 font-label-stat text-label-stat text-on-surface">
            <span class="material-symbols-outlined text-sm text-secondary">collections</span>
            {{ $account->images_count ?? 1 }} Foto
        </span>
    </a>

    <div class="{{ $bodyClasses }}">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 mb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $game?->icon_color ?? '#d0bcff' }}"></span>
                    <span class="font-label-stat text-label-stat uppercase tracking-wider text-outline">{{ $game?->name ?? 'GameVault' }}</span>
                    @if(($account->instant_delivery ?? true))
                        <span class="inline-flex items-center gap-0.5 rounded bg-secondary-container/30 text-secondary font-label-stat text-[10px] px-1.5 py-0.5 font-semibold">
                            <span class="material-symbols-outlined text-[11px]">bolt</span>INSTANT
                        </span>
                    @endif
                </div>
                <a href="{{ route('marketplace.show', $account) }}" class="font-body-md text-body-md font-semibold text-on-surface hover:text-secondary transition-colors leading-snug line-clamp-2">{{ $account->title }}</a>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-1.5">
            @if($account->rank)
                <span class="inline-flex items-center gap-1 rounded-md bg-surface-container-highest px-1.5 py-1 font-label-stat text-label-stat font-medium text-secondary">
                    <span class="material-symbols-outlined text-xs">military_tech</span>{{ $account->rank }}
                </span>
            @endif
            @if($account->level)
                <span class="inline-flex items-center gap-1 rounded-md bg-surface-container-highest px-1.5 py-1 font-label-stat text-label-stat font-medium text-on-surface-variant">
                    <span class="material-symbols-outlined text-xs">bolt</span>Lv {{ $account->level }}
                </span>
            @endif
            @if($account->heros_count)
                <span class="inline-flex items-center gap-1 rounded-md bg-surface-container-highest px-1.5 py-1 font-label-stat text-label-stat font-medium text-on-surface-variant">
                    <span class="material-symbols-outlined text-xs">group</span>{{ number_format($account->heros_count, 0, ',', '.') }} Hero
                </span>
            @endif
            @if(isset($account->rarity_metrics['skins']) || $account->skins_count)
                <span class="inline-flex items-center gap-1 rounded-md bg-surface-container-highest px-1.5 py-1 font-label-stat text-label-stat font-medium text-tertiary">
                    <span class="material-symbols-outlined text-xs">star</span>{{ number_format($account->skins_count ?? $account->rarity_metrics['skins'], 0, ',', '.') }} Skin
                </span>
            @endif
        </div>

        <div class="flex items-center gap-1.5 mt-auto">
            <span class="material-symbols-outlined text-xs text-secondary">star</span>
            <span class="font-label-stat text-label-stat font-semibold text-on-surface">5.0</span>
            <span class="material-symbols-outlined text-xs text-outline">visibility</span>
            <span class="font-label-stat text-label-stat text-on-surface-variant">{{ number_format($account->views_count ?? 0, 0, ',', '.') }}x dilihat</span>
        </div>

        <div class="h-px w-full bg-outline-variant/30 my-1"></div>

        <div class="flex items-end justify-between gap-2">
            <div class="flex flex-col">
                @if($strike && $strike > $price)
                    <span class="font-label-stat text-label-stat text-outline line-through decoration-outline/50 mb-0.5">{{ 'Rp ' . number_format($strike, 0, ',', '.') }}</span>
                @endif
                <span class="font-mono font-bold text-secondary text-[15px] leading-none">{{ 'Rp ' . number_format($final, 0, ',', '.') }}</span>
            </div>
            <button type="button"
                class="wishlist-btn inline-flex items-center gap-1.5 px-3 py-2 rounded-lg font-body-sm text-body-sm font-semibold transition-all {{ $wishlisted ? 'bg-secondary-container/30 text-secondary' : 'bg-surface-container-highest text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}"
                data-account="{{ $account->id }}"
                @guest
                onclick="window.location.href='{{ route('login') }}'"
                @endguest
                aria-label="Tambah ke wishlist">
                <span class="material-symbols-outlined text-sm {{ $wishlisted ? '' : '' }}">{{ $wishlisted ? 'favorite' : 'favorite_border' }}</span>
            </button>
        </div>

        <a href="{{ route('marketplace.show', $account) }}" class="mt-1 flex items-center justify-center gap-1.5 w-full px-3 py-2.5 rounded-lg bg-primary-container/20 text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary hover:text-on-primary transition-all">
            <span class="material-symbols-outlined text-sm">storefront</span>Lihat Detail
        </a>
    </div>
</div>