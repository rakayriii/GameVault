@extends('layouts.app')

@section('title', $gameAccount->title . ' - GameVault')

@section('content')
    @php($active = 'browse')

    @include('partials.flash')

    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-20 right-10 w-80 h-80 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-6 relative">
            <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-8 overflow-x-auto whitespace-nowrap py-1">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">home</span>
                    <span>Home</span>
                </a>
                <span class="text-outline-variant font-label-mono">/</span>
                <a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Browse Accounts</a>
                <span class="text-outline-variant font-label-mono">/</span>
                <a href="{{ route('marketplace.index', ['game' => $gameAccount->game->slug]) }}" class="hover:text-primary transition-colors">{{ $gameAccount->game->name }}</a>
                <span class="text-outline-variant font-label-mono">/</span>
                <span class="text-on-surface font-medium truncate max-w-md">{{ $gameAccount->title }}</span>
                <span class="px-2 py-0.5 rounded bg-surface-container-high text-outline font-label-mono text-label-stat tracking-wider">#GV-{{ str_pad((string) $gameAccount->id, 6, '0', STR_PAD_LEFT) }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">
                {{-- LEFT: GALLERY --}}
                <div class="lg:col-span-7 flex flex-col gap-4" x-data="gallery({{ Illuminate\Support\Js::from($gallery) }})">
                    <div class="relative w-full rounded-2xl overflow-hidden bg-surface-container-low shadow-xl group">
                        <div class="absolute top-4 left-4 right-4 z-20 flex flex-wrap items-center justify-between gap-2 pointer-events-none">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full bg-tertiary-container/30 backdrop-blur-md text-tertiary font-label-stat text-label-stat font-semibold flex items-center gap-1.5 shadow-sm">
                                    <span class="material-symbols-outlined text-xs">verified</span>
                                    Tangan Pertama
                                </span>
                                @if($gameAccount->instant_delivery)
                                    <span class="px-3 py-1 rounded-full bg-secondary-container/30 backdrop-blur-md text-secondary font-label-stat text-label-stat font-semibold flex items-center gap-1.5 shadow-sm">
                                        <span class="material-symbols-outlined text-xs">bolt</span>
                                        Instant Delivery &lt;10m
                                    </span>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full bg-error-container/40 backdrop-blur-md text-error font-label-stat text-label-stat font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">shield_with_heart</span>
                                Garansi Anti-Hackback 30 Hari
                            </span>
                        </div>

                        <div class="relative aspect-[16/10] w-full overflow-hidden bg-gradient-to-br from-surface-container-high via-surface-container to-surface-container-lowest flex items-center justify-center">
                            @if($gameAccount->images->isNotEmpty())
                                <template x-for="(img, i) in images" :key="i">
                                    <img :src="img.url" :alt="img.caption" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300" :class="i === active ? 'opacity-100' : 'opacity-0'">
                                </template>
                            @else
                                <span class="text-7xl font-extrabold font-headline-sm text-on-surface/10 select-none tracking-tighter">{{ str($gameAccount->game->name)->upper()->substr(0, 2) }}</span>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-surface-container-low via-transparent to-transparent"></div>
                            <button type="button" @click="zoomed = true" class="absolute bottom-4 right-4 px-3.5 py-1.5 rounded-xl bg-surface-container-highest/85 backdrop-blur-md text-on-surface font-label-mono text-label-stat hover:bg-primary hover:text-on-primary transition-all flex items-center gap-1.5 shadow-lg">
                                <span class="material-symbols-outlined text-base">zoom_in</span>
                                Inspect 4K Screenshots
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-3">
                        @if($gameAccount->images->isNotEmpty())
                            <template x-for="(img, i) in images" :key="i">
                                <button type="button" @click="active = i" class="group relative aspect-video rounded-xl overflow-hidden bg-surface-container-high p-0.5 transition-all"
                                    :class="i === active ? 'ring-2 ring-primary' : 'opacity-70 hover:opacity-100'">
                                    <img :src="img.url" :alt="img.caption" class="w-full h-full rounded-lg object-cover">
                                    <span class="absolute bottom-1.5 right-1.5 px-1 rounded bg-surface-container-lowest/80 text-[10px] font-label-mono text-on-surface" x-text="img.caption"></span>
                                </button>
                            </template>
                        @else
                            @for($i = 0; $i < 5; $i++)
                                <div class="group relative aspect-video rounded-xl overflow-hidden bg-surface-container-high p-0.5 {{ $i === 0 ? 'ring-2 ring-primary' : 'opacity-70 hover:opacity-100' }} transition-all">
                                    <div class="w-full h-full rounded-lg bg-gradient-to-br from-surface-container-lowest via-surface-container to-surface-container-high flex items-center justify-center">
                                        <span class="material-symbols-outlined text-2xl text-on-surface/20">{{ ['photo_camera','style','monitor_heart','inventory_2','security'][$i] }}</span>
                                    </div>
                                    <span class="absolute bottom-1 right-1 px-1 rounded bg-surface-container-lowest/80 text-[10px] font-label-mono text-on-surface">{{ ['Lobby','Skins','Emblem','Inventory','Bind Proof'][$i] }}</span>
                                </div>
                            @endfor
                        @endif
                    </div>

                    {{-- Zoom modal --}}
                    <div x-show="zoomed" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" @click="zoomed = false">
                        <div class="max-w-3xl w-full rounded-2xl overflow-hidden bg-surface-container-low p-2">
                            <div class="relative aspect-[16/10] w-full rounded-xl overflow-hidden bg-gradient-to-br from-surface-container-high via-surface-container to-surface-container-lowest">
                                @if($gameAccount->images->isNotEmpty())
                                    <template x-for="(img, i) in images" :key="i">
                                        <img :src="img.url" :alt="img.caption" class="absolute inset-0 w-full h-full object-contain" x-show="i === active">
                                    </template>
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-8xl font-extrabold font-headline-sm text-on-surface/10 select-none">{{ str($gameAccount->game->name)->upper()->substr(0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Seller corner --}}
                    <a href="{{ route('store.show', $gameAccount->seller->sellerProfile->slug) }}" class="p-4 rounded-2xl bg-surface-container-low flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl {{ $gameAccount->seller->sellerProfile->is_official_verified ? 'bg-tertiary-container/30 text-tertiary' : 'bg-secondary-container/30 text-secondary' }} flex items-center justify-center font-bold font-label-stat text-label-stat">
                                {{ str($gameAccount->seller->sellerProfile->store_name)->upper()->substr(0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-sm text-sm font-bold text-on-surface group-hover:text-primary transition-colors">{{ $gameAccount->seller->sellerProfile->store_name }}</span>
                                <span class="font-label-stat text-label-stat text-on-surface-variant">
                                    {{ number_format($gameAccount->seller->sellerProfile->total_sales, 0, ',', '.') }} transaksi • Rating {{ number_format($gameAccount->seller->sellerProfile->rating_cache, 1, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">arrow_forward</span>
                    </a>
                </div>

                {{-- RIGHT: BUY CARD --}}
                <div class="lg:col-span-5 flex flex-col gap-4 relative">
                    <div class="rounded-2xl bg-surface-container-low border border-outline-variant/20 p-6 flex flex-col gap-4 relative overflow-hidden">
                        <div class="absolute -top-16 -right-16 w-48 h-48 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="flex items-center gap-2">
<span class="px-2.5 py-1 rounded-full bg-primary-container/20 text-primary font-badge text-badge font-semibold flex items-center gap-1.5">
                                    @if($gameAccount->game->iconUrl())
                                        <img src="{{ $gameAccount->game->iconUrl() }}" alt="" class="h-4 w-4 rounded">
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $gameAccount->game->icon_color }}"></span>
                                    @endif
                                    {{ $gameAccount->game->name }}
                                </span>
                            @if($gameAccount->discount_percent)
                                <span class="px-2.5 py-1 rounded-full bg-tertiary-container/30 text-tertiary font-badge text-badge font-semibold">Drop {{ $gameAccount->discount_percent }}%</span>
                            @endif
                        </div>

                        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">{{ $gameAccount->title }}</h1>

                        @if($gameAccount->rank)
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-3 py-1 rounded-lg bg-surface-container-highest text-secondary font-label-stat text-label-stat font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">military_tech</span>{{ $gameAccount->rank }}
                                </span>
                                @if($gameAccount->level)
                                    <span class="px-3 py-1 rounded-lg bg-surface-container-highest text-on-surface-variant font-label-stat text-label-stat flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">bolt</span>Level {{ $gameAccount->level }}
                                    </span>
                                @endif
                                @if($gameAccount->heros_count)
                                    <span class="px-3 py-1 rounded-lg bg-surface-container-highest text-on-surface-variant font-label-stat text-label-stat flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">group</span>{{ $gameAccount->heros_count }} Hero
                                    </span>
                                @endif
                                @if($gameAccount->skins_count)
                                    <span class="px-3 py-1 rounded-lg bg-surface-container-highest text-tertiary font-label-stat text-label-stat flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">star</span>{{ $gameAccount->skins_count }} Skin
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="flex items-baseline gap-3 pt-1">
                            @if($gameAccount->discount_percent)
                                <span class="font-label-mono text-label-stat text-outline line-through">{{ 'Rp ' . number_format($gameAccount->strike_price, 0, ',', '.') }}</span>
                            @endif
                            <span class="font-headline-xl text-headline-xl font-bold text-on-surface">{{ 'Rp ' . number_format($gameAccount->price, 0, ',', '.') }}</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-tertiary text-sm">lock</span>
                            Bisa cicilan 0% atau bayar via QRIS, BCA, DANA, GoPay. Dana aman dalam escrow GameVault.
                        </p>

                        <div class="p-4 rounded-2xl bg-surface-container flex flex-col gap-2.5 relative overflow-hidden">
                            <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-tertiary/10 rounded-full blur-2xl pointer-events-none"></div>
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-lg bg-tertiary-container/30 text-tertiary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl">security</span>
                                </div>
                                <div class="flex flex-col">
                                    <h2 class="font-headline-sm text-sm font-bold text-on-surface">Jaminan Perlindungan Escrow 100%</h2>
                                    <span class="font-label-stat text-[10px] text-tertiary uppercase tracking-widest">Sistem Rekber 3-Fase Terenkripsi</span>
                                </div>
                            </div>
                            <p class="font-body-sm text-[13px] text-on-surface-variant leading-relaxed">
                                Uang kamu disimpan aman oleh sistem. Seller <span class="text-on-surface font-semibold underline decoration-secondary">HANYA menerima dana</span> setelah kamu mengonfirmasi penggantian email dan password berhasil tanpa kendala.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                            <a href="{{ auth()->check() ? route('checkout.create', $gameAccount) : route('login') }}" class="w-full sm:flex-1 py-4 px-6 rounded-xl bg-gradient-to-r from-primary-container via-inverse-primary to-primary text-on-primary font-headline-sm text-headline-sm font-bold hover:opacity-95 transition-all shadow-[0_10px_30px_-5px_rgba(160,120,255,0.4)] flex items-center justify-center gap-2 text-center">
                                <span class="material-symbols-outlined text-xl">enhanced_encryption</span>
                                <span>Beli Sekarang (Escrow)</span>
                            </a>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" class="wishlist-btn flex-1 sm:flex-initial py-4 px-5 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-error hover:bg-surface-container transition-all flex items-center justify-center gap-1.5" data-account="{{ $gameAccount->id }}" aria-label="Simpan ke Wishlist">
                                    <span class="material-symbols-outlined text-xl {{ $wishlisted ? 'text-error' : '' }}">{{ $wishlisted ? 'favorite' : 'favorite' }}</span>
                                </button>
                                <button onclick="window.Vault.copy(window.location.href); window.Vault.showToast('link','Link akun disalin!')" type="button" aria-label="Bagikan Listing" class="py-4 px-5 rounded-xl bg-surface-container-high text-on-surface-variant hover:text-secondary hover:bg-surface-container transition-all flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl">share</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Seller identity --}}
                    <div class="p-4 rounded-2xl bg-surface-container-low flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-xl {{ $gameAccount->seller->sellerProfile->is_official_verified ? 'bg-tertiary-container/30 text-tertiary' : 'bg-secondary-container/30 text-secondary' }} flex items-center justify-center font-bold font-label-stat text-label-stat">
                                        {{ str($gameAccount->seller->sellerProfile->store_name)->upper()->substr(0, 1) }}
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full bg-tertiary ring-2 ring-surface-container-low"></span>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-headline-sm text-sm font-bold text-on-surface">{{ $gameAccount->seller->sellerProfile->store_name }}</span>
                                        @if($gameAccount->seller->sellerProfile->is_official_verified)
                                            <span class="px-1.5 py-0.5 rounded bg-secondary-container/20 text-secondary font-label-stat text-[10px] font-bold">OFFICIAL</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-on-surface-variant font-label-mono text-label-stat">
                                        <span class="text-tertiary flex items-center gap-0.5"><span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">star</span> {{ number_format($gameAccount->seller->sellerProfile->rating_cache, 1, ',', '.') }}</span>
                                        <span>•</span>
                                        <span>{{ number_format($gameAccount->seller->sellerProfile->total_sales, 0, ',', '.') }} Transaksi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-label-stat text-[10px] text-tertiary uppercase tracking-wider block">Online Sekarang</span>
                                <span class="font-label-mono text-label-stat text-outline">Balas &lt; {{ $gameAccount->seller->sellerProfile->response_time_minutes }} mnt</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <a href="{{ route('store.show', $gameAccount->seller->sellerProfile->slug) }}" class="py-2.5 px-3 rounded-xl bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high font-body-sm text-body-sm font-medium flex items-center justify-center gap-1.5 transition-colors text-center">
                                <span class="material-symbols-outlined text-sm">storefront</span>
                                <span>Kunjungi Toko</span>
                            </a>
                            <a href="{{ route('marketplace.index', ['game' => $gameAccount->game->slug]) }}" class="py-2.5 px-3 rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-body-sm text-body-sm font-semibold flex items-center justify-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-sm text-secondary">apps</span>
                                <span>Listing Lain</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL & REVIEWS --}}
            <div class="w-full bg-surface-container-low rounded-2xl p-6 lg:p-8 mb-16">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="lg:w-2/3 flex flex-col gap-6">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-primary font-label-stat text-label-stat uppercase tracking-wider mb-2">
                                <span class="material-symbols-outlined text-sm">query_stats</span>
                                <span>Detail Akun &amp; Statistik</span>
                            </div>
                            <h2 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-4">Spesifikasi Lengkap</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @if($gameAccount->level)
                                    @include('partials.spec-cell', ['label' => 'Level Akun', 'value' => (string) $gameAccount->level, 'color' => 'text-on-surface', 'hint' => 'Season aktif'])
                                @endif
                                @if($gameAccount->heros_count)
                                    @include('partials.spec-cell', ['label' => 'Total Hero', 'value' => (string) $gameAccount->heros_count, 'color' => 'text-secondary', 'hint' => 'All Unlocked'])
                                @endif
                                @if($gameAccount->skins_count)
                                    @include('partials.spec-cell', ['label' => 'Total Skin', 'value' => (string) $gameAccount->skins_count, 'color' => 'text-primary', 'hint' => 'Limited & Premium'])
                                @endif
                                @if($gameAccount->winrate)
                                    @include('partials.spec-cell', ['label' => 'Winrate', 'value' => (string) $gameAccount->winrate . '%', 'color' => 'text-tertiary', 'hint' => 'Overall'])
                                @endif
                            </div>
                        </div>

                        <div class="h-px w-full bg-outline-variant/20"></div>

                        <div>
                            <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary">verified</span> Fitur Unggulan
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($gameAccount->features ?? [] as $feature)
                                    <span class="px-3 py-1.5 rounded-full bg-surface-container-highest text-on-surface-variant font-body-sm text-body-sm flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-tertiary">{{ $feature['icon'] ?? 'star' }}</span>
                                        {{ $feature['label'] ?? 'Fitur' }}
                                    </span>
                                @empty
                                    <span class="px-3 py-1.5 rounded-full bg-surface-container-highest text-on-surface-variant font-body-sm text-body-sm">Akun murni tanpa sangkutan</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="h-px w-full bg-outline-variant/20"></div>

                        <div>
                            <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-tertiary">description</span> Deskripsi Akun
                            </h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $gameAccount->description }}</p>
                        </div>
                    </div>

                    <div class="lg:w-1/3 flex flex-col gap-4">
                        <div class="p-5 rounded-2xl bg-surface-container">
                            <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-4">Keamanan &amp; Legitimasi</h3>
                            <ul class="flex flex-col gap-3">
                                @foreach([
                                    ['icon' => 'lock', 'label' => 'Email & sandi bisa diganti', 'ok' => true],
                                    ['icon' => 'link_off', 'label' => 'Semua binding sudah di-unlink', 'ok' => true],
                                    ['icon' => 'shield', 'label' => 'Garansi anti-hackback 30 hari', 'ok' => true],
                                    ['icon' => 'history_edu', 'label' => 'Bukti kepemilikan 1st hand', 'ok' => true],
                                ] as $item)
                                    <li class="flex items-center gap-2.5 font-body-sm text-body-sm text-on-surface-variant">
                                        <span class="w-7 h-7 rounded-lg {{ $item['ok'] ? 'bg-tertiary-container/20 text-tertiary' : 'bg-error-container/20 text-error' }} flex items-center justify-center">
                                            <span class="material-symbols-outlined text-sm">{{ $item['icon'] }}</span>
                                        </span>
                                        {{ $item['label'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="p-5 rounded-2xl bg-surface-container">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Ulasan Pembeli</h3>
                                <span class="px-2 py-1 rounded-lg bg-surface-container-highest text-tertiary font-label-stat text-label-stat font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">star</span>{{ number_format($gameAccount->seller->sellerProfile->rating_cache, 1, ',', '.') }}
                                </span>
                            </div>
                            @forelse($reviews as $review)
                                <div class="py-3 {{ !$loop->first ? 'border-t border-outline-variant/20' : '' }}">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <x-avatar :user="$review->reviewer" class="w-7 h-7 text-[10px] rounded-full" />
                                        <span class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $review->reviewer->username }}</span>
                                        <span class="ml-auto"><x-rating :value="$review->rating" size="text-xs" /></span>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">"{{ $review->content }}"</p>
                                </div>
                            @empty
                                <x-empty-state icon="reviews" title="Belum ada ulasan" description="Jadilah pembeli pertama yang mengulas akun ini." />
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- RELATED --}}
            @if($related->count())
                <section class="mb-16">
                    <x-section-header title="Akun Serupa" subtitle="Jelajahi akun lain dari kategori yang sama." />
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mt-5">
                        @foreach($related as $account)
                            <x-account-card :account="$account" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection