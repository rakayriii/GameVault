@extends('layouts.app')

@section('title', 'Seller Center · GameVault')

@section('content')
    @php $profile = auth()->user()->sellerProfile; @endphp

    <div class="max-w-[1200px] mx-auto px-6 lg:px-8 py-10">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Seller Center</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">{{ $profile?->store_name }} · {{ auth()->user()->username }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('seller.accounts.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">add_circle</span>Jual Akun Baru
                </a>
                <a href="{{ route('store.show', $profile) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-lg">storefront</span>Lihat Toko
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            @php
                $cards = [
                    ['label' => 'Total Listing', 'value' => (string) $total_accounts, 'icon' => 'inventory_2'],
                    ['label' => 'Aktif / Live', 'value' => (string) $active_accounts, 'icon' => 'visibility', 'accent' => 'text-tertiary'],
                    ['label' => 'Menunggu Review', 'value' => (string) $pending_accounts, 'icon' => 'hourglass_top', 'accent' => 'text-secondary'],
                    ['label' => 'Transaksi Selesai', 'value' => (string) $total_sales, 'icon' => 'shopping_bag'],
                    ['label' => 'Pendapatan', 'value' => 'Rp ' . number_format($revenue, 0, ',', '.'), 'icon' => 'payments', 'accent' => 'text-tertiary'],
                    ['label' => 'Rating', 'value' => $rating ? number_format($rating, 1) . ' ★' : '—', 'icon' => 'star', 'accent' => 'text-secondary'],
                ];
            @endphp
            @foreach($cards as $c)
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="material-symbols-outlined {{ $c['accent'] ?? 'text-on-surface-variant' }}">{{ $c['icon'] }}</span>
                    <div class="mt-3 font-headline-sm text-headline-sm font-bold text-on-surface truncate">{{ $c['value'] }}</div>
                    <div class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">{{ $c['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-3 gap-6 items-start mb-10">
            <a href="{{ route('seller.accounts') }}" class="block rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 hover:border-secondary/50 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="font-body-md text-body-md font-bold text-on-surface">Listing Akun</span>
                    <span class="material-symbols-outlined text-secondary">chevron_right</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Kelola akun, edit harga, atur live / offline.</p>
            </a>
            <a href="{{ route('seller.orders') }}" class="block rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 hover:border-secondary/50 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="font-body-md text-body-md font-bold text-on-surface">Pesanan Masuk</span>
                    <span class="flex items-center gap-2">
                        @if($actionable_orders > 0)
                            <span class="px-2 py-0.5 rounded-full bg-secondary-container/40 text-secondary font-label-stat text-label-stat font-bold">{{ $actionable_orders }} siap handover</span>
                        @elseif($pending_payment_orders > 0)
                            <span class="px-2 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-stat text-label-stat font-bold">{{ $pending_payment_orders }} menunggu bayar</span>
                        @endif
                        <span class="material-symbols-outlined text-secondary">chevron_right</span>
                    </span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Proses handover di ruang transaksi aman.</p>
            </a>
            <a href="{{ route('seller.store') }}" class="block rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 hover:border-secondary/50 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="font-body-md text-body-md font-bold text-on-surface">Profil Toko</span>
                    <span class="material-symbols-outlined text-secondary">chevron_right</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Ubah nama toko, bio, pengumuman.</p>
            </a>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 items-start">
            <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Pesanan Terbaru</h2>
                    <a href="{{ route('seller.orders') }}" class="text-secondary font-body-sm text-body-sm font-semibold">Semua</a>
                </div>
                @forelse($recentOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="px-6 py-3.5 border-b border-outline-variant/10 flex items-center gap-4 last:border-0 hover:bg-surface-container transition-colors">
                        @if($order->gameAccount?->images->first())
                            <img src="{{ $order->gameAccount->images->first()->url() }}" class="w-11 h-11 rounded-2xl object-cover shrink-0">
                        @else
                            <x-game-icon :game="$order->gameAccount?->game" size="w-11 h-11 shrink-0" icon="text-xl" />
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $order->gameAccount?->title }}</p>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $order->buyer?->username }} · {{ $order->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="block font-label-mono text-label-mono text-on-surface">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                @if($order->status->value === 'completed') bg-tertiary-container/30 text-tertiary
                                @elseif(in_array($order->status->value, ['escrow','handover'])) bg-secondary-container/30 text-secondary
                                @elseif($order->status->value === 'disputed') bg-error-container/30 text-error
                                @else bg-surface-container text-on-surface-variant @endif">{{ $order->status->label() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline">receipt_long</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">Belum ada pesanan masuk.</p>
                    </div>
                @endforelse
            </section>

            <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Status Listing</h2>
                    <a href="{{ route('seller.accounts') }}" class="text-secondary font-body-sm text-body-sm font-semibold">Kelola</a>
                </div>
                @forelse($recentAccounts as $acc)
                    <div class="px-6 py-4 border-b border-outline-variant/10 flex items-center gap-4 last:border-0">
                        <x-game-icon :game="$acc->game" size="w-11 h-11" />
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $acc->title }}</p>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">Rp {{ number_format($acc->price, 0, ',', '.') }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full font-badge text-badge text-[10px] font-semibold
                            {{ $acc->status->value === 'approved' ? 'bg-tertiary-container/30 text-tertiary' : ($acc->status->value === 'pending_review' ? 'bg-secondary-container/30 text-secondary' : ($acc->status->value === 'sold' ? 'bg-primary-container/30 text-primary' : 'bg-surface-container text-on-surface-variant')) }}">
                            {{ $acc->status->label() }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline">inventory_2</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">Jual akun pertamamu sekarang.</p>
                        <a href="{{ route('seller.accounts.create') }}" class="inline-flex items-center gap-1.5 mt-3 text-secondary font-body-sm text-body-sm font-semibold"><span class="material-symbols-outlined text-sm">add_circle</span>Buat Listing</a>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection