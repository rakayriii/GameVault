@extends('layouts.app')

@section('title', $store->store_name . ' - GameVault')

@section('content')
    <div class="relative w-full max-w-[1340px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant/80">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Marketplace</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <a href="{{ route('marketplace.index', ['game' => 'mlbb']) }}" class="hover:text-primary transition-colors">Penjual Terverifikasi</a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-on-surface font-medium truncate">{{ $store->store_name }}</span>
        </nav>

        {{-- STORE HERO BANNER --}}
        <section class="relative w-full rounded-2xl overflow-hidden bg-surface-container-low shadow-2xl">
            <div class="relative h-64 sm:h-72 lg:h-80 w-full overflow-hidden">
                <div class="w-full h-full bg-gradient-to-br from-primary/20 via-surface-container-high to-secondary/20"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-surface-container-low via-surface-container-low/75 to-transparent"></div>
                <div class="absolute top-4 right-4 sm:top-6 sm:right-6 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-highest/80 backdrop-blur-md text-tertiary font-label-stat text-label-stat font-bold uppercase tracking-wider">
                        <span class="h-2 w-2 rounded-full bg-tertiary animate-pulse"></span>
                        Online Sekarang
                    </span>
                    @if($store->is_official_verified)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-primary-container/40 backdrop-blur-md text-secondary font-label-stat text-label-stat font-semibold">
                            <span class="material-symbols-outlined text-xs">verified</span>
                            {{ $store->membership_tier ?? 'Verified Merchant' }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- PROFILE BAR --}}
            <div class="relative px-6 sm:px-8 pb-8 -mt-20">
                <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">
                        <div class="relative group">
                            <div class="h-28 w-28 sm:h-32 sm:w-32 rounded-2xl p-1 bg-gradient-to-br from-primary via-secondary to-tertiary shadow-xl">
                                <div class="w-full h-full rounded-xl overflow-hidden {{ $store->is_official_verified ? 'bg-tertiary-container/20' : 'bg-secondary-container/20' }} flex items-center justify-center">
                                    <span class="font-headline-sm text-5xl font-extrabold text-{{ $store->is_official_verified ? 'tertiary' : 'secondary' }}">{{ str($store->store_name)->upper()->substr(0, 1) }}</span>
                                </div>
                            </div>
                            @if($store->is_official_verified)
                                <div class="absolute -bottom-2 -right-2 h-7 w-7 rounded-full bg-surface-container-low flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-primary text-lg" style="font-variation-settings: 'FILL' 1;">verified</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">{{ $store->store_name }}</h1>
                                @if($store->is_official_verified)
                                    <span class="px-2.5 py-0.5 rounded-full bg-tertiary-container/30 text-tertiary font-badge text-badge font-semibold inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">shield</span> Escrow Verified
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-container/20 text-secondary font-badge text-badge font-semibold">
                                        Official Pro Merchant
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-3 font-body-sm text-body-sm text-on-surface-variant">
                                <span class="font-label-mono text-label-mono text-secondary-fixed">@{{ $store->slug }}</span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-outline">calendar_month</span>
                                    Bergabung {{ $store->seller->created_at?->format('M Y') }}
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-outline">zone_person_urgent</span>
                                    Respon &lt; {{ $store->response_time_minutes }} Menit
                                </span>
                            </div>
                            @if($store->bio)
                                <p class="font-body-sm text-body-sm text-on-surface-variant max-w-xl mt-1">{{ $store->bio }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('marketplace.index', ['game' => 'mlbb']) }}" class="px-5 py-3 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center gap-2 hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-lg">storefront</span>
                            Buy from {{ $store->store_name }}
                        </a>
                    </div>
                </div>

                {{-- STATS --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                    @foreach($stats as $stat)
                        <div class="p-4 rounded-2xl bg-surface-container flex flex-col gap-1">
                            <span class="material-symbols-outlined {{ $stat['accent'] }}">{{ $stat['icon'] }}</span>
                            <span class="font-headline-md text-headline-md text-on-surface font-bold font-label-mono">{{ $stat['value'] }}</span>
                            <span class="font-label-stat text-label-stat text-on-surface-variant">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        @if($store->announcement)
            <div class="p-4 rounded-2xl bg-surface-container-low flex items-center gap-3 border border-primary/20">
                <span class="material-symbols-outlined text-primary">campaign</span>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $store->announcement }}</p>
            </div>
        @endif

        {{-- LISTINGS --}}
        <section class="flex flex-col gap-5">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Koleksi Akun {{ $store->store_name }}</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Semua listing dijamin aman oleh sistem escrow GameVault.</p>
                </div>
                <span class="px-3 py-1.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-stat text-label-stat">{{ $store->accounts_count ?? $store->accounts->count() }} Listing</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($store->accounts as $account)
                    <x-account-card :account="$account" />
                @empty
                    <div class="col-span-full bg-surface-container-low rounded-2xl p-12 flex flex-col items-center gap-4 text-center">
                        <span class="material-symbols-outlined text-6xl text-outline">inventory_2</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Belum ada listing</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant max-w-md">Toko ini belum memiliki akun aktif. Cek kembali beberapa saat lagi.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection