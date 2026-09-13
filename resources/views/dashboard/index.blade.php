@extends('layouts.app')

@section('title', 'Dashboard · GameVault')

@section('content')
    <div class="max-w-[1200px] mx-auto px-6 lg:px-8 py-10">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Halo, {{ auth()->user()->username }} 👋</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola pesanan, wishlist & saldo di sini.</p>
            </div>
            <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-lg">search</span>Jelajahi Marketplace
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @php
                $cards = [
                    ['label' => 'Transaksi Aktif', 'value' => (string) $activeOrders, 'icon' => 'shopping_bag', 'link' => route('orders.index')],
                    ['label' => 'Saldo Rekber', 'value' => $walletBalance !== null ? 'Rp ' . number_format($walletBalance, 0, ',', '.') : '—', 'icon' => 'account_balance_wallet', 'link' => route('wallet.index')],
                    ['label' => 'Wishlist', 'value' => (string) $wishlistCount, 'icon' => 'favorite', 'link' => route('wishlist.index')],
                    ['label' => 'Semua Pesanan', 'value' => (string) $orders->count(), 'icon' => 'receipt_long', 'link' => route('orders.index')],
                ];
            @endphp
            @foreach($cards as $c)
                <a href="{{ $c['link'] }}" class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 hover:border-secondary/40 transition-colors">
                    <span class="material-symbols-outlined text-secondary">{{ $c['icon'] }}</span>
                    <div class="mt-3 font-headline-sm text-headline-sm font-bold text-on-surface truncate">{{ $c['value'] }}</div>
                    <div class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">{{ $c['label'] }}</div>
                </a>
            @endforeach
        </div>

        <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
            <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Pesanan Terbaru</h2>
                <a href="{{ route('orders.index') }}" class="text-secondary font-body-sm text-body-sm font-semibold">Semua</a>
            </div>
            @forelse($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="px-6 py-4 border-b border-outline-variant/10 flex items-center gap-4 last:border-0 hover:bg-surface-container/60 transition-colors">
                    <x-game-icon :game="$order->gameAccount?->game" size="w-11 h-11 rounded-2xl" icon="text-xl" />
                    <div class="flex-1 min-w-0">
                        <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $order->gameAccount?->title }}</p>
                        <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $order->order_no }} · {{ $order->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full font-badge text-badge text-[10px] font-semibold
                        {{ $order->status->value === 'completed' ? 'bg-tertiary-container/30 text-tertiary' : ($order->status->value === 'escrow' || $order->status->value === 'handover' ? 'bg-secondary-container/30 text-secondary' : 'bg-surface-container text-on-surface-variant') }}">
                        {{ $order->status->label() }}
                    </span>
                    <span class="font-label-mono text-label-mono text-on-surface shrink-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-5xl text-outline">receipt_long</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-2">Belum ada pesanan.</p>
                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-1.5 mt-3 text-secondary font-body-sm text-body-sm font-semibold">Cari akun impianmu</a>
                </div>
            @endforelse
        </section>
    </div>
@endsection