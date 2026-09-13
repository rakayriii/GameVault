@extends('layouts.app')

@section('title', 'Keranjang - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 right-10 w-96 h-96 bg-secondary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-10 relative">
            <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="material-symbols-outlined text-xs text-outline">chevron_right</span>
                <span class="text-secondary font-medium">Keranjang</span>
            </nav>

            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold mb-2 flex items-center gap-3">
                Keranjang Belanja
                @if($items->count())
                    <span class="px-2.5 py-1 rounded-full bg-primary-container/20 text-primary font-label-stat text-label-stat font-bold">{{ $items->count() }} item</span>
                @endif
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant mb-8">Percepat checkout — akun yang kamu incar hanya tinggal selangkah lagi.</p>

            @if($items->isEmpty())
                <div class="bg-surface-container-low rounded-2xl p-12 flex flex-col items-center gap-4 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">shopping_cart</span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Keranjang kamu masih kosong</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                        Tambahkan akun favorit ke keranjang sebelum checkout. Atau langsung beli melalui tombol escrow.
                    </p>
                    <a href="{{ route('marketplace.index') }}" class="mt-2 py-3 px-6 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center gap-2 hover:bg-primary transition-all">
                        <span class="material-symbols-outlined text-lg">search</span>
                        Jelajahi Marketplace
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    <div class="lg:col-span-2 flex flex-col gap-4">
                        @foreach($items as $item)
                            @php($account = $item->gameAccount)
                            <div class="p-5 rounded-2xl bg-surface-container-low flex flex-col sm:flex-row gap-4">
                                <div class="w-full sm:w-40 shrink-0 relative aspect-video rounded-xl overflow-hidden bg-gradient-to-br from-surface-container-high via-surface-container to-surface-container-lowest flex items-center justify-center">
                                    <span class="text-4xl font-extrabold font-headline-sm text-on-surface/10 select-none">{{ str($account->game->name)->upper()->substr(0, 2) }}</span>
                                </div>
                                <div class="flex-1 flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full" style="background: {{ $account->game->icon_color }}22; color: {{ $account->game->icon_color }}">
                                            <span class="text-[10px] font-bold">{{ $account->game->name }}</span>
                                        </span>
                                        <span class="font-label-stat text-label-stat text-on-surface-variant">{{ $account->rank }}</span>
                                    </div>
                                    <a href="{{ route('marketplace.show', $account) }}" class="font-headline-sm text-headline-sm text-on-surface font-bold hover:text-primary transition-colors">
                                        {{ $account->title }}
                                    </a>
                                    <div class="flex items-center gap-3 text-on-surface-variant font-label-stat text-label-stat">
                                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">military_tech</span>{{ $account->rank }}</span>
                                        @if($account->heros_count)<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">group</span>{{ $account->heros_count }} Hero</span>@endif
                                        @if($account->skins_count)<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">star</span>{{ $account->skins_count }} Skin</span>@endif
                                    </div>
                                    <div class="flex items-center justify-between pt-2 mt-auto">
                                        <div class="flex items-baseline gap-2">
                                            @if($account->discount_percent)
                                                <span class="font-label-mono text-label-stat text-outline line-through">{{ 'Rp ' . number_format($account->strike_price, 0, ',', '.') }}</span>
                                            @endif
                                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ 'Rp ' . number_format($account->price, 0, ',', '.') }}</span>
                                        </div>
                                        <form method="POST" action="{{ route('cart.remove', $account) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-2 rounded-xl bg-surface-container text-on-surface-variant hover:text-error hover:bg-surface transition-colors flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-lg">delete</span> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-4 sticky top-28">
                        <div class="rounded-2xl bg-surface-container-low border border-outline-variant/20 p-6 flex flex-col gap-4">
                            <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Ringkasan Belanja</h2>
                            <div class="flex flex-col gap-2 font-body-sm text-body-sm text-on-surface-variant">
                                <div class="flex justify-between"><span>Subtotal ({{ $items->count() }} akun)</span><span class="text-on-surface font-semibold font-label-mono">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Biaya Escrow (5%)</span><span class="text-tertiary font-semibold font-label-mono">Gratis (via Saldo) / {{ 'Rp ' . number_format(min(round($total * 0.05), 1000000), 0, ',', '.') }}</span></div>
                                <div class="h-px w-full bg-outline-variant/20 my-1"></div>
                                <div class="flex justify-between items-center">
                                    <span class="text-on-surface font-bold">Total Bayar</span>
                                    <span class="font-headline-sm text-headline-sm font-bold text-on-surface font-label-mono">{{ 'Rp ' . number_format($total + min(round($total * 0.05), 1000000), 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('checkout.create', $items->first()->gameAccount) }}" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-primary-container via-inverse-primary to-primary text-on-primary font-headline-sm text-sm font-bold flex items-center justify-center gap-2 hover:opacity-95 transition-all shadow-[0_10px_30px_-5px_rgba(160,120,255,0.4)]">
                                <span class="material-symbols-outlined text-lg">enhanced_encryption</span>
                                Checkout dengan Escrow
                            </a>
                            <p class="font-body-sm text-[12px] text-on-surface-variant text-center flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-sm text-tertiary">security</span>
                                Dana aman dalam escrow sampai handover berhasil.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection