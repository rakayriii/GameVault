@extends('layouts.app')

@section('title', 'Admin Console · GameVault')

@section('content')
    <div class="max-w-[1280px] mx-auto px-6 lg:px-8 py-10">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Admin Console</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Ringkasan platform GameVault.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Orders <span class="ml-1 px-1.5 py-0.5 rounded-md bg-secondary-container/40 text-secondary text-[10px]">{{ $stats['orders'] }}</span>
                </a>
                <a href="{{ route('admin.rekber') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Rekber <span class="ml-1 px-1.5 py-0.5 rounded-md bg-secondary-container/40 text-secondary text-[10px]">{{ $stats['escrow_active'] }}</span>
                </a>
                <a href="{{ route('admin.accounts') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Review Listing <span class="ml-1 px-1.5 py-0.5 rounded-md bg-secondary-container/40 text-secondary text-[10px]">{{ $stats['pending_accounts'] }}</span>
                </a>
                <a href="{{ route('admin.seller-requests') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Seller Request <span class="ml-1 px-1.5 py-0.5 rounded-md bg-secondary-container/40 text-secondary text-[10px]">{{ $stats['pending_seller_requests'] }}</span>
                </a>
                <a href="{{ route('admin.games') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Games
                </a>
                <a href="{{ route('admin.withdrawals') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                    Withdrawal <span class="ml-1 px-1.5 py-0.5 rounded-md bg-secondary-container/40 text-secondary text-[10px]">{{ $stats['pending_withdrawals'] }}</span>
                </a>
                <a href="{{ route('admin.disputes') }}" class="px-4 py-2.5 rounded-xl bg-error-container/30 text-error font-body-sm text-body-sm font-semibold hover:bg-error-container/50 transition-colors">
                    Dispute <span class="ml-1 px-1.5 py-0.5 rounded-md bg-error/20 text-error text-[10px]">{{ $stats['open_disputes'] }}</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4 mb-8">
            @php
                $statCards = [
                    ['label' => 'Total User', 'value' => (string) $stats['users'], 'icon' => 'group'],
                    ['label' => 'Buyer', 'value' => (string) $stats['buyers'], 'icon' => 'person'],
                    ['label' => 'Seller', 'value' => (string) $stats['sellers'], 'icon' => 'storefront'],
                    ['label' => 'Total Listing', 'value' => (string) $stats['accounts'], 'icon' => 'inventory_2'],
                    ['label' => 'Live Listing', 'value' => (string) $stats['live_accounts'], 'icon' => 'visibility', 'accent' => 'text-tertiary'],
                    ['label' => 'Order Total', 'value' => (string) $stats['orders'], 'icon' => 'receipt_long'],
                    ['label' => 'Menunggu Bayar', 'value' => (string) $stats['pending_orders'], 'icon' => 'schedule', 'accent' => 'text-secondary'],
                    ['label' => 'Escrow Aktif', 'value' => (string) $stats['escrow_active'], 'icon' => 'lock', 'accent' => 'text-secondary'],
                    ['label' => 'Dispute', 'value' => (string) $stats['disputed_orders'], 'icon' => 'gavel', 'accent' => 'text-error'],
                    ['label' => 'Order Selesai', 'value' => (string) $stats['completed_orders'], 'icon' => 'task_alt'],
                    ['label' => 'Dana Tersedia', 'value' => 'Rp ' . number_format($stats['wallet_available'], 0, ',', '.'), 'icon' => 'account_balance_wallet', 'accent' => 'text-tertiary'],
                    ['label' => 'Dana Escrow Vault', 'value' => 'Rp ' . number_format($stats['wallet_escrow'], 0, ',', '.'), 'icon' => 'lock_clock', 'accent' => 'text-tertiary'],
                    ['label' => 'GMV', 'value' => 'Rp ' . number_format($stats['gmv'], 0, ',', '.'), 'icon' => 'payments', 'accent' => 'text-tertiary'],
                ];
            @endphp
            @foreach($statCards as $c)
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-4">
                    <span class="material-symbols-outlined {{ $c['accent'] ?? 'text-on-surface-variant' }}">{{ $c['icon'] }}</span>
                    <div class="mt-2 font-label-mono text-label-mono font-bold text-on-surface truncate">{{ $c['value'] }}</div>
                    <div class="font-label-stat text-[10px] text-on-surface-variant uppercase tracking-wider">{{ $c['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-2 gap-6 items-start">
            <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Order Terbaru</h2>
                    <a href="{{ route('admin.orders') }}" class="text-secondary font-body-sm text-body-sm font-semibold">Semua</a>
                </div>
                @forelse($recentOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="block px-6 py-3.5 border-b border-outline-variant/10 flex items-center gap-3 last:border-0 hover:bg-surface-container transition-colors">
                        <span class="font-label-mono text-label-mono text-outline shrink-0">{{ $order->order_no }}</span>
                        <span class="flex-1 min-w-0 font-body-sm text-body-sm text-on-surface truncate">{{ $order->gameAccount?->title }}</span>
                        <span class="font-label-mono text-label-mono text-on-surface shrink-0">{{ number_format($order->total_amount, 0, '.', ',') }}</span>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge text-[10px] shrink-0">{{ $order->status->label() }}</span>
                    </a>
                @empty
                    <div class="px-6 py-10 text-center font-body-sm text-body-sm text-on-surface-variant">Belum ada order.</div>
                @endforelse
            </section>

            <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Listing Menunggu Review</h2>
                    <a href="{{ route('admin.accounts') }}" class="text-secondary font-body-sm text-body-sm font-semibold">Review</a>
                </div>
                @forelse($recentAccounts as $acc)
                    <div class="px-6 py-3.5 border-b border-outline-variant/10 flex items-center gap-3 last:border-0">
                        <div class="w-10 h-10 rounded-xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0 overflow-hidden">
                            @if($acc->game?->iconUrl())
                                <img src="{{ $acc->game->iconUrl() }}" alt="{{ $acc->game->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-on-surface-variant">sports_esports</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $acc->title }}</p>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">Seller {{ $acc->seller?->username }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-secondary-container/30 text-secondary font-badge text-badge text-[10px] shrink-0">Menunggu Review</span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center font-body-sm text-body-sm text-on-surface-variant">Tidak ada listing menunggu.</div>
                @endforelse
            </section>
        </div>
    </div>
@endsection