@extends('layouts.app')

@section('title', 'Pesanan Saya · GameVault')

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Pesanan Saya</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Pantau status pembayaran, escrow, handover, sampai dispute.</p>
        </div>

        <div class="flex gap-2 overflow-x-auto pb-2 mb-6">
            @php
                $tabs = ['' => 'Semua', 'pending_payment' => 'Menunggu Bayar', 'escrow' => 'Escrow', 'handover' => 'Handover', 'completed' => 'Selesai', 'disputed' => 'Dispute', 'cancelled' => 'Batal'];
            @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('orders.index', $val ? ['status' => $val] : []) }}"
                   class="shrink-0 px-4 py-2 rounded-full font-body-sm text-body-sm font-semibold transition-colors {{ $status === $val ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container text-on-surface-variant hover:text-on-surface' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @forelse($orders as $order)
            <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 mb-4 flex flex-wrap items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0">
                    <x-game-icon :game="$order->gameAccount?->game" size="w-16 h-16" icon="text-3xl" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-label-mono text-label-mono text-outline">{{ $order->order_no }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                            @if(in_array($order->status->value, ['completed'])) bg-tertiary-container/30 text-tertiary
                            @elseif(in_array($order->status->value, ['escrow','handover'])) bg-secondary-container/30 text-secondary
                            @elseif($order->status->value === 'disputed') bg-error-container/30 text-error
                            @else bg-surface-container text-on-surface-variant @endif">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                    <h3 class="font-body-md text-body-md font-semibold text-on-surface mt-1 line-clamp-1">{{ $order->gameAccount?->title ?? $order->items->first()?->account_title }}</h3>
                    <p class="font-label-stat text-label-stat text-on-surface-variant mt-0.5">
                        {{ $order->gameAccount?->game?->name }} · Seller {{ $order->seller?->username }} · {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="font-headline-sm text-headline-sm font-bold text-tertiary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    <div class="mt-2 flex gap-2 justify-end">
                        @if($order->status->value === 'pending_payment')
                            <a href="{{ route('checkout.payment', $order) }}" class="px-4 py-2 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">Bayar Sekarang</a>
                        @elseif(in_array($order->status->value, ['escrow','handover','buyer_confirmation']))
                            <a href="{{ route('orders.handover', $order) }}" class="px-4 py-2 rounded-xl bg-secondary-container/40 text-secondary font-body-sm text-body-sm font-semibold hover:bg-secondary hover:text-on-secondary transition-colors">Buka Ruang Handover</a>
                        @endif
                        <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold hover:text-on-surface transition-colors">Detail</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                <span class="material-symbols-outlined text-6xl text-outline">receipt_long</span>
                <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-4">Belum ada pesanan</h3>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Temukan akun impianmu dan mulai transaksi aman dengan rekber.</p>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-lg">storefront</span>Jelajahi Marketplace
                </a>
            </div>
        @endforelse

        <div class="mt-8">{{ $orders->links() }}</div>
    </div>
@endsection