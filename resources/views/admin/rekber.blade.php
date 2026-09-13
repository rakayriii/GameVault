@extends('layouts.app')

@section('title', 'Rekber & Escrow Vault · Admin')

@section('content')
    <div class="max-w-[1220px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Rekber & Escrow Vault</span>
        </nav>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface flex items-center gap-3">
                    Rekber & Escrow Vault
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-tertiary-container/25 text-tertiary font-badge text-badge font-semibold">
                        <span class="material-symbols-outlined text-sm">lock</span>LIVE MONITOR
                    </span>
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Posisi dana rekber, transaksi escrow berjalan, dan mutasi wallet platform.</p>
            </div>
            <a href="{{ route('admin.withdrawals') }}" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                Proses Penarikan
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-4 mb-8">
            @php
                $cards = [
                    ['label' => 'Saldo Tersedia', 'value' => 'Rp ' . number_format($vault['available'], 0, ',', '.'), 'icon' => 'savings', 'accent' => 'text-tertiary'],
                    ['label' => 'Dana Terkunci Vault', 'value' => 'Rp ' . number_format($vault['escrow'], 0, ',', '.'), 'icon' => 'lock', 'accent' => 'text-secondary'],
                    ['label' => 'Total Dana Rekber', 'value' => 'Rp ' . number_format($vault['total'], 0, ',', '.'), 'icon' => 'account_balance', 'accent' => 'text-tertiary'],
                    ['label' => 'Wallet Aktif', 'value' => (string) $vault['wallets'], 'icon' => 'account_balance_wallet'],
                    ['label' => 'Menunggu Bayar', 'value' => (string) $vault['pending_count'], 'icon' => 'schedule', 'accent' => 'text-outline'],
                    ['label' => 'Nilai Menunggu Bayar', 'value' => 'Rp ' . number_format($vault['pending_value'], 0, ',', '.'), 'icon' => 'hourglass_top'],
                    ['label' => 'Escrow Aktif', 'value' => (string) $vault['escrow_count'], 'icon' => 'lock_clock', 'accent' => 'text-secondary'],
                    ['label' => 'Nilai Escrow Aktif', 'value' => 'Rp ' . number_format($vault['escrow_value'], 0, ',', '.'), 'icon' => 'shield', 'accent' => 'text-secondary'],
                ];
            @endphp
            @foreach($cards as $c)
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
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">swap_horiz</span>Transaksi Berjalan
                    </h2>
                </div>
                @forelse($inflight as $order)
                    <div class="px-6 py-3.5 border-b border-outline-variant/10 last:border-0 flex flex-wrap items-center gap-3">
                        <span class="font-label-mono text-label-mono text-outline shrink-0">{{ $order->order_no }}</span>
                        <span class="flex-1 min-w-0 font-body-sm text-body-sm text-on-surface truncate">{{ $order->gameAccount?->title }}</span>
                        <span class="px-2 py-0.5 rounded-full font-badge text-badge text-[10px] shrink-0
                            {{ $order->status->value === 'pending_payment' ? 'bg-surface-container text-on-surface-variant' : 'bg-secondary-container/30 text-secondary' }}">
                            {{ $order->status->label() }}
                        </span>
                        <span class="font-label-mono text-label-mono text-tertiary shrink-0">Rp {{ number_format($order->total_amount, 0, '.', ',') }}</span>
                        <a href="{{ route('orders.show', $order) }}" class="shrink-0 inline-flex items-center gap-1 rounded-xl bg-surface-container px-3 py-1.5 text-on-surface-variant font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center font-body-sm text-body-sm text-on-surface-variant">Tidak ada transaksi berjalan.</div>
                @endforelse
                @if($inflight->hasPages())
                    <div class="px-6 py-4">{{ $inflight->links() }}</div>
                @endif
            </section>

            <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-tertiary">receipt_long</span>Mutasi Wallet Terbaru
                    </h2>
                </div>
                @forelse($transactions as $tx)
                    <div class="px-6 py-3 border-b border-outline-variant/10 last:border-0 flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                            {{ $tx->direction->value === 'in' ? 'bg-tertiary-container/25 text-tertiary' : 'bg-error-container/25 text-error' }}">
                            <span class="material-symbols-outlined text-base">{{ $tx->direction->value === 'in' ? 'arrow_downward' : 'arrow_upward' }}</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-body-sm text-body-sm text-on-surface truncate">{{ $tx->description }}</p>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">
                                {{ $tx->wallet?->user?->username ?? '—' }} · {{ $tx->created_at->translatedFormat('d M Y, H:i') }}
                            </p>
                        </div>
                        <span class="font-label-mono text-label-mono {{ $tx->direction->value === 'in' ? 'text-tertiary' : 'text-error' }} shrink-0">
                            {{ $tx->direction->value === 'in' ? '+' : '−' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center font-body-sm text-body-sm text-on-surface-variant">Belum ada mutasi.</div>
                @endforelse
            </section>
        </div>
    </div>
@endsection