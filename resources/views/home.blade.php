@extends('layouts.app')

@section('title', 'GameVault - Temukan Akun Game Impianmu')

@section('content')
    @php($active = 'home')

    @include('partials.flash')

    {{-- HERO --}}
    <section class="relative w-full overflow-hidden bg-surface pb-16 lg:pb-24">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[850px] h-[450px] bg-gradient-to-b from-primary-container/20 via-secondary/15 to-transparent blur-3xl pointer-events-none rounded-full"></div>
        <div class="absolute top-48 -left-36 w-80 h-80 bg-primary/10 blur-[120px] pointer-events-none rounded-full"></div>
        <div class="absolute top-36 -right-36 w-96 h-96 bg-secondary/10 blur-[120px] pointer-events-none rounded-full"></div>

        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 pt-8 lg:pt-14 relative z-10 flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-surface-container-high text-tertiary shadow-sm mb-6 transition-transform hover:scale-105">
                <span class="material-symbols-outlined text-base text-tertiary" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                <span class="font-badge text-badge font-semibold tracking-wide text-on-surface">#1 Marketplace Akun Game Terpercaya di Indonesia • <span class="text-tertiary font-bold">120.000+ Transaksi Sukses</span></span>
            </div>

            <h1 class="font-headline-xl text-headline-xl lg:font-display lg:text-display text-on-surface max-w-4xl tracking-tight leading-tight lg:leading-none">
                Akun Game Impianmu, <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-tertiary">Ada di Sini.</span>
            </h1>
            <p class="mt-5 font-body-lg text-body-lg text-on-surface-variant max-w-2xl leading-relaxed">
                Temukan akun game berkualitas dari seller terverifikasi dengan sistem rekber otomatis &amp; garansi uang kembali 100%.
            </p>

            <form action="{{ route('marketplace.index') }}" method="GET" class="w-full max-w-4xl mt-10 p-2.5 rounded-2xl bg-surface-container-low shadow-xl">
                <div class="flex flex-col md:flex-row items-stretch gap-2">
                    <div class="relative flex items-center bg-surface-container rounded-xl px-4 py-3 shrink-0">
                        <span class="material-symbols-outlined text-secondary text-xl mr-2.5">sports_esports</span>
                        <select name="game" class="bg-transparent font-body-sm text-body-sm text-on-surface focus:outline-none appearance-none pr-8 cursor-pointer font-medium">
                            <option value="">Semua Game</option>
                            @foreach($games as $g)
                                <option value="{{ $g->slug }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined text-on-surface-variant text-base absolute right-3 pointer-events-none">expand_more</span>
                    </div>
                    <div class="relative flex-1 flex items-center bg-surface-container rounded-xl px-4 py-3">
                        <span class="material-symbols-outlined text-outline text-xl mr-3">search</span>
                        <input name="q" class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none" placeholder="Cari nama game, rank (Mythical Glory, Radiant), skin langka, level..." type="text">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-headline-sm font-semibold transition-all hover:bg-primary shadow-lg shadow-primary-container/30 shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-xl">travel_explore</span>
                        <span>Cari Akun</span>
                    </button>
                </div>
                <div class="flex flex-wrap items-center justify-start gap-2 pt-3 px-2">
                    <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider">Populer:</span>
                    @foreach($games->take(6) as $index => $g)
                        <a href="{{ route('marketplace.index', ['game' => $g->slug]) }}" class="px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-secondary font-label-mono text-label-stat transition-colors flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $g->icon_color }}"></span>{{ $g->name }}
                        </a>
                    @endforeach
                </div>
            </form>

            <div class="w-full max-w-5xl mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-2xl bg-surface-container-low/80 backdrop-blur-md">
                @foreach($stats as $stat)
                    <div class="flex items-center gap-3.5 p-3 rounded-xl bg-surface-container">
                        <div class="w-10 h-10 rounded-xl {{ $stat['icon'] === 'verified_user' ? 'bg-tertiary-container/20' : 'bg-surface-container-high' }} {{ $stat['accent'] }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-2xl">{{ $stat['icon'] }}</span>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $stat['value'] }}</span>
                            <span class="font-label-stat text-label-stat text-on-surface-variant uppercase">{{ $stat['label'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- POPULAR CATEGORIES --}}
    <section class="w-full bg-surface-container-lowest py-16">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8">
            <x-section-header
                title="Kategori Game Populer"
                :subtitle="'Pilih game favoritmu dan temukan ribuan akun siap pakai dari penjual tepercaya.'"
                actionLabel="Lihat Semua Kategori"
                :actionUrl="route('marketplace.index')" />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-4">
                @foreach($games->take(8) as $game)
                    <x-game-card :game="$game" :count="$game->accounts_count" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- FEATURED LISTINGS --}}
    <section class="w-full py-16 bg-surface">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
                <div>
                    <div class="flex items-center gap-2 text-primary font-label-stat text-label-stat uppercase tracking-wider mb-2">
                        <span class="material-symbols-outlined text-sm">local_fire_department</span>
                        <span>Pilihan Sultan &amp; Akun Siap Tempur</span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg font-bold text-on-surface">Akun Pilihan Terlaris</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2 p-1.5 rounded-xl bg-surface-container-low">
                    <a class="px-4 py-2 rounded-lg bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold shadow-sm transition-all" href="{{ route('marketplace.index') }}">🔥 Rekomendasi Hari Ini</a>
                    <a class="px-4 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-body-sm text-body-sm font-medium transition-all" href="{{ route('marketplace.index', ['instant' => 1]) }}">⚡ Instant Delivery</a>
                    <a class="px-4 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-body-sm text-body-sm font-medium transition-all" href="{{ route('marketplace.index') }}">💎 Akun Sultan</a>
                    <a class="px-4 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-body-sm text-body-sm font-medium transition-all" href="{{ route('marketplace.index') }}">🏷️ Promo Diskon</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($trending as $account)
                    <x-account-card :account="$account" :wishlisted="$account->id && false" />
                @empty
                    <x-empty-state icon="storefront" title="Belum ada akun dipasang" description="Daftar jadi penjual dan jual akun pertamamu sekarang." />
                @endforelse
            </div>
        </div>
    </section>

    {{-- TRUSTED ESCROW --}}
    <section class="w-full bg-surface-container-lowest py-20 relative overflow-hidden">
        <div class="absolute -right-20 top-1/2 -translate-y-1/2 w-96 h-96 bg-tertiary/10 blur-[130px] pointer-events-none rounded-full"></div>
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5 flex flex-col">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-tertiary-container/20 text-tertiary font-label-stat text-label-stat uppercase tracking-wider mb-4 w-fit">
                        <span class="material-symbols-outlined text-sm">enhanced_encryption</span>
                        <span>Security First Infrastructure</span>
                    </div>
                    <h2 class="font-headline-xl text-headline-xl font-bold text-on-surface tracking-tight leading-tight">
                        Kenapa Belanja di <span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary to-tertiary">GameVault?</span>
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-4 leading-relaxed">
                        Marketplace akun game pertama di Indonesia dengan Rekber Otomatis 3-Fase. Transaksi aman tanpa risiko tipu-menipu, akun ditarik kembali (hackback), ataupun sengketa dana.
                    </p>
                    <div class="mt-8 p-5 rounded-2xl bg-surface-container flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-tertiary text-on-tertiary flex items-center justify-center shrink-0 shadow-lg shadow-tertiary/20">
                            <span class="material-symbols-outlined text-2xl font-bold">gshield</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">Garansi 100% Saldo Kembali</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Jika data akun tidak sesuai spesifikasi atau seller tidak mengirimkan akses dalam waktu 30 menit, saldo direfund secara otomatis tanpa potongan.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['icon' => 'badge', 'color' => 'text-secondary', 'bg' => 'bg-secondary-container/20', 'title' => 'Verified Sellers Only', 'desc' => 'Seller wajib melewati KYC verifikasi KTP, nomor WhatsApp aktif, dan riwayat rekening bank sebelum diizinkan memasang iklan.'],
                        ['icon' => 'lock_clock', 'color' => 'text-tertiary', 'bg' => 'bg-tertiary-container/20', 'title' => 'Saldo Tertahan Aman (Vault)', 'desc' => 'Uang pembeli disimpan dalam rekening escrow pintar GameVault dan baru dicairkan ke seller setelah pembeli mengonfirmasi akun aman.'],
                        ['icon' => 'policy', 'color' => 'text-primary', 'bg' => 'bg-primary-container/20', 'title' => 'Garansi 30 Hari Anti-Hackback', 'desc' => 'Semua transaksi dilindungi polis proteksi 30 hari. Kami memediasi dan mengganti dana apabila akun di-hackback seller asal.'],
                        ['icon' => 'sync_saved_locally', 'color' => 'text-secondary', 'bg' => 'bg-secondary-container/20', 'title' => 'Serah Terima Otomatis & Terpandu', 'desc' => 'Sistem memberikan tutorial interaktif step-by-step untuk unlink nomor telepon, ganti email pertama, dan aktivasi 2-Factor Authentication.'],
                    ] as $pillar)
                        <div class="p-5 rounded-2xl bg-surface-container hover:bg-surface-container-high transition-colors flex flex-col gap-2.5">
                            <div class="w-10 h-10 rounded-xl {{ $pillar['bg'] }} {{ $pillar['color'] }} flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl">{{ $pillar['icon'] }}</span>
                            </div>
                            <h4 class="font-headline-sm text-sm font-bold text-on-surface">{{ $pillar['title'] }}</h4>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $pillar['desc'] }}</p>
                        </div>
                    @endforeach
                    <div class="sm:col-span-2 p-5 rounded-2xl bg-surface-container-high hover:bg-surface-bright transition-colors flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary text-on-primary flex items-center justify-center shrink-0 shadow-md">
                            <span class="material-symbols-outlined text-2xl">support_agent</span>
                        </div>
                        <div class="flex flex-col">
                            <h4 class="font-headline-sm text-sm font-bold text-on-surface">Customer Service Siaga 24/7 (Live Agent)</h4>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Bantuan penengah sengketa transaksi kapan saja dengan waktu respon rata-rata di bawah 3 menit.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="w-full py-20 bg-surface">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-1.5 text-secondary font-label-stat text-label-stat uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-sm">route</span>
                    <span>Panduan Escrow 4 Langkah</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg font-bold text-on-surface">Cara Transaksi Mudah &amp; Aman</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Dapatkan akun game impianmu dalam hitungan menit tanpa rasa cemas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach($steps as $step)
                    <div class="p-6 rounded-2xl bg-surface-container-low flex flex-col justify-between hover:bg-surface-container transition-all">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="w-10 h-10 rounded-xl bg-surface-container-high {{ $step['color'] }} flex items-center justify-center font-label-mono text-label-mono font-bold">{{ $loop->iteration }}<span class="text-[10px]">/</span></span>
                                <span class="material-symbols-outlined text-2xl {{ $step['color'] }}">{{ $step['icon'] }}</span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-2">{{ $step['title'] }}</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TOP SELLERS --}}
    <section class="w-full py-16 bg-surface-container-lowest">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8">
            <x-section-header
                title="Top Verified Sellers"
                subtitle="Penjual berprestasi dengan rekam jejak ribuan transaksi sukses dan reputasi bintang 5."
                actionLabel="Semua Seller Terverifikasi"
                :actionUrl="route('marketplace.index')" />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-4">
                @forelse($topSellers as $seller)
                    <div class="p-5 rounded-2xl bg-surface-container-low flex flex-col justify-between hover:bg-surface-container transition-all group">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full bg-surface-container-high {{ $seller->is_verified ? 'text-tertiary' : 'text-secondary' }} flex items-center justify-center font-bold font-label-stat text-label-stat shadow-md">
                                    {{ str($seller->store_name)->upper()->substr(0, 1) }}
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-1">
                                        <h4 class="font-headline-sm text-headline-sm font-bold text-on-surface truncate">{{ $seller->store_name }}</h4>
                                        <span class="material-symbols-outlined text-secondary text-sm shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                                    </div>
                                    <span class="font-label-stat text-label-stat text-primary uppercase font-semibold">Verified Seller</span>
                                </div>
                            </div>
                            <div class="space-y-2 py-3 bg-surface-container px-3 rounded-xl">
                                <div class="flex justify-between font-label-mono text-label-stat">
                                    <span class="text-on-surface-variant">Transaksi:</span>
                                    <span class="font-semibold text-on-surface">{{ number_format($seller->total_sales, 0, ',', '.') }}+ Selesai</span>
                                </div>
                                <div class="flex justify-between font-label-mono text-label-stat">
                                    <span class="text-on-surface-variant">Rating:</span>
                                    <span class="font-semibold text-tertiary flex items-center gap-0.5"><span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">star</span> {{ number_format($seller->rating_cache, 1, ',', '.') }} / 5.0</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('store.show', $seller->slug) }}" class="mt-4 w-full py-2 rounded-xl bg-surface-container-high hover:bg-primary-container hover:text-on-primary-container text-on-surface font-body-sm text-body-sm font-semibold text-center transition-colors">Kunjungi Toko</a>
                    </div>
                @empty
                    <x-empty-state icon="storefront" title="Belum ada seller terverifikasi" class="lg:col-span-4" />
                @endforelse
            </div>
        </div>
    </section>

    {{-- CALL TO ACTION --}}
    <section class="w-full py-16 bg-surface">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-surface-container-high via-surface-container to-surface-container-high p-8 lg:p-14 shadow-2xl">
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-container/30 blur-3xl pointer-events-none rounded-full"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-secondary/20 blur-3xl pointer-events-none rounded-full"></div>
                <div class="relative z-10 max-w-2xl flex flex-col items-start">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-secondary-container/20 text-secondary font-label-stat text-label-stat uppercase tracking-wider mb-4">
                        <span class="material-symbols-outlined text-sm">monetization_on</span>
                        <span>Program Penjual GameVault</span>
                    </div>
                    <h2 class="font-headline-lg text-headline-lg lg:font-headline-xl lg:text-headline-xl font-bold text-on-surface tracking-tight leading-tight">
                        Punya Akun Game yang Mau Dijual? <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-tertiary">Ubah Jadi Uang Tunai Sekarang.</span>
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-4 leading-relaxed">
                        Daftar sebagai seller terverifikasi, nikmati potongan komisi terendah hanya <span class="text-tertiary font-bold">2.5%</span>, dan jangkau lebih dari <span class="text-on-surface font-bold">500.000+ gamers</span> aktif di seluruh Indonesia.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 mt-8">
                        <a href="{{ auth()->check() ? route('seller.accounts.create') : route('register') }}" class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-xl bg-primary-container text-on-primary-container hover:bg-primary font-headline-sm text-headline-sm font-semibold transition-all shadow-lg shadow-primary-container/30">
                            <span class="material-symbols-outlined text-xl">sports_esports</span>
                            <span>Mulai Jual Akun</span>
                        </a>
                        <a href="{{ route('help.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-surface-container-highest hover:bg-surface-bright text-on-surface font-headline-sm text-headline-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-xl">info</span>
                            <span>Pelajari Syarat Seller</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection