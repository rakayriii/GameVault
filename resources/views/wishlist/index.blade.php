@extends('layouts.app')

@section('title', 'Wishlist - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 right-10 w-80 h-80 bg-tertiary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-10 relative">
            <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="material-symbols-outlined text-xs text-outline">chevron_right</span>
                <span class="text-secondary font-medium">Wishlist</span>
            </nav>

            <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold flex items-center gap-3">
                        Wishlist <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                        {{ $items->count() }} akun kamu simpan untuk dipertimbangkan.
                    </p>
                </div>
                <a href="{{ route('marketplace.index') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface font-body-sm text-body-sm font-semibold flex items-center gap-2 hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-primary text-sm">compass_calibration</span>
                    Jelajahi Marketplace
                </a>
            </div>

            @if($items->isEmpty())
                <div class="bg-surface-container-low rounded-2xl p-12 flex flex-col items-center gap-4 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">favorite_border</span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Wishlist kamu masih kosong</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                        Simpan akun incaran dengan tombol <span class="material-symbols-outlined text-error text-sm align-middle">favorite</span> di setiap kartu akun.
                    </p>
                    <a href="{{ route('marketplace.index') }}" class="mt-2 py-3 px-6 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center gap-2 hover:bg-primary transition-all">
                        <span class="material-symbols-outlined text-lg">search</span>
                        Mulai Cari Akun
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($items as $item)
                        <x-account-card :account="$item->gameAccount" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection