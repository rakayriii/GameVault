@extends('layouts.app')

@section('title', 'Browse Accounts - GameVault Marketplace')

@section('content')
    @php($active = 'browse')

    @include('partials.flash')

    <section class="w-full bg-surface-container-lowest">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-6">
            <nav class="flex items-center gap-2 font-body-sm text-body-sm text-outline mb-3">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="text-outline-variant select-none">/</span>
                <span class="text-on-surface-variant font-medium">Browse Accounts</span>
                <span class="text-outline-variant select-none">/</span>
                <span class="text-secondary font-medium">{{ $activeGame?->name ?? 'Semua Game' }}</span>
                <span class="ml-2 px-2 py-0.5 rounded-full bg-surface-container-high text-outline text-label-stat font-label-mono">{{ number_format($accounts->total(), 0, ',', '.') }} Akun Tersedia</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">
                        Eksplorasi Akun Game <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-tertiary">Terverifikasi</span>
                    </h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-2xl">
                        Semua akun terjamin garansi rekber anti-hackback, validasi identitas penjual, dan serah terima instan kurang dari 10 menit.
                    </p>
                </div>
                <div class="flex items-center gap-2 self-start md:self-auto px-3.5 py-1.5 rounded-xl bg-tertiary-container/15 text-tertiary font-label-stat text-label-stat font-semibold tracking-wide shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-base">verified_user</span>
                    <span>100% Saldo Ditahan Hingga Akun Aman</span>
                </div>
            </div>

            <div class="flex items-center gap-2 mt-6 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl {{ $activeGame ? 'bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' : 'bg-primary text-on-primary' }} font-body-sm text-body-sm font-semibold shadow-md shrink-0 transition-all">
                    <span class="material-symbols-outlined text-base">apps</span>
                    <span>Semua Game</span>
                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-on-primary/10 text-on-primary font-label-stat text-label-stat">{{ number_format($accounts->total(), 0, ',', '.') }}</span>
                </a>
                @foreach($games as $game)
                    <a href="{{ route('marketplace.index', ['game' => $game->slug]) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl {{ $activeGame?->slug === $game->slug ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }} font-body-sm text-body-sm shrink-0 transition-all">
                        @if($game->iconUrl())
                            <img src="{{ $game->iconUrl() }}" alt="{{ $game->name }}" class="w-4 h-4 rounded">
                        @else
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $game->icon_color }}"></span>
                        @endif
                        <span>{{ $game->name }}</span>
                        <span class="font-label-mono text-label-stat text-outline">{{ number_format($game->accounts_count, 0, ',', '.') }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="max-w-[1340px] mx-auto px-6 lg:px-8 py-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <aside class="lg:col-span-3 flex flex-col gap-6 lg:sticky lg:top-36">
                <form action="{{ route('marketplace.index') }}" method="GET" class="p-5 rounded-2xl bg-surface-container-low shadow-sm flex flex-col gap-5">
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    <div class="flex items-center justify-between pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-xl">tune</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Filter Pencarian</h2>
                        </div>
                        <a href="{{ route('marketplace.index') }}" class="font-body-sm text-body-sm text-secondary hover:underline">Reset</a>
                    </div>

                    <div class="flex flex-col gap-3 p-3.5 rounded-xl bg-surface-container">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-lg">bolt</span>
                                <span class="font-body-sm text-body-sm text-on-surface font-medium">Pengiriman Instan (&lt;10m)</span>
                            </div>
                            <input type="checkbox" name="instant" value="1" {{ request()->boolean('instant') ? 'checked' : '' }} class="w-4 h-4 rounded bg-surface-container-high accent-secondary cursor-pointer">
                        </label>
                        <label class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-tertiary text-lg">shield</span>
                                <span class="font-body-sm text-body-sm text-on-surface font-medium">Garansi Hackback 30 Hari</span>
                            </div>
                            <input type="checkbox" checked disabled class="w-4 h-4 rounded bg-surface-container-high accent-tertiary cursor-pointer">
                        </label>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-label-stat text-label-stat uppercase text-outline tracking-wider font-semibold">Pilih Game</label>
                        <div class="relative">
                            <select name="game" class="w-full appearance-none px-3.5 py-2.5 rounded-xl bg-surface-container text-on-surface font-body-sm text-body-sm focus:outline-none focus:bg-surface-container-high cursor-pointer pr-10">
                                <option value="">Semua Kategori Game</option>
                                @foreach($games as $game)
                                    <option value="{{ $game->slug }}" {{ $activeGame?->slug === $game->slug ? 'selected' : '' }}>{{ $game->name }} ({{ number_format($game->accounts_count, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-2.5 text-on-surface-variant pointer-events-none text-xl">expand_more</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <label class="font-label-stat text-label-stat uppercase text-outline tracking-wider font-semibold">Rentang Harga (IDR)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min" value="{{ request('min') }}" placeholder="Min" min="0" class="w-full px-2.5 py-2 rounded-lg bg-surface-container text-on-surface font-label-mono text-label-stat placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-secondary">
                            <input type="number" name="max" value="{{ request('max') }}" placeholder="Max" min="0" class="w-full px-2.5 py-2 rounded-lg bg-surface-container text-on-surface font-label-mono text-label-stat placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-secondary">
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="font-label-stat text-label-stat uppercase text-outline tracking-wider font-semibold">Urutkan</label>
                        <select name="sort" class="w-full appearance-none px-3.5 py-2.5 rounded-xl bg-surface-container text-on-surface font-body-sm text-body-sm focus:outline-none cursor-pointer">
                            <option value="latest" {{ request('sort') === 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                            <option value="cheapest" {{ request('sort') === 'cheapest' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="expensive" {{ request('sort') === 'expensive' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Paling Populer</option>
                        </select>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary-container text-on-primary-container hover:bg-primary font-body-sm text-body-sm font-semibold transition-all">
                        <span class="material-symbols-outlined text-base">search</span>
                        Terapkan Filter
                    </button>
                </form>
            </aside>

            <div class="lg:col-span-9 flex flex-col gap-6">
                @forelse($accounts as $account)
                    <x-account-card :account="$account" layout="row" :wishlisted="$wishlistIds->has($account->id)" />
                @empty
                    <x-empty-state icon="search_off" title="Tidak ada akun ditemukan" description="Coba ubah atau reset filter pencarianmu untuk melihat akun lain.">
                        <a href="{{ route('marketplace.index') }}" class="mt-2 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-all">
                            <span class="material-symbols-outlined text-base">refresh</span>Reset Filter
                        </a>
                    </x-empty-state>
                @endforelse

                <div class="mt-4">
                    {{ $accounts->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection