@extends('layouts.app')

@section('title', 'Order Management · Admin')

@section('content')
    <div class="max-w-[1220px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Order Management</span>
        </nav>

        <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Order Management</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Pantau semua transaksi, escrow, handover, sampai dispute.</p>
            </div>
            <form method="GET" action="{{ route('admin.orders') }}" class="flex items-end gap-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                    <input name="q" value="{{ request('q') }}" placeholder="Cari order / akun / user..." class="w-72 rounded-xl bg-surface-container border border-outline-variant/30 pl-10 pr-3 py-2.5 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">Cari</button>
            </form>
        </div>

        <div class="flex gap-2 overflow-x-auto pb-2 mb-6">
            @php
                $tabs = ['' => 'Semua', 'pending_payment' => 'Menunggu Bayar', 'escrow' => 'Escrow', 'handover' => 'Handover', 'completed' => 'Selesai', 'disputed' => 'Dispute', 'cancelled' => 'Batal'];
            @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('admin.orders', array_filter(['status' => $val, 'q' => request('q')])) }}"
                   class="shrink-0 px-4 py-2 rounded-full font-body-sm text-body-sm font-semibold transition-colors {{ $status === $val ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container text-on-surface-variant hover:text-on-surface' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @forelse($orders as $order)
            <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 mb-4 flex flex-wrap items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0 overflow-hidden">
                    @if($order->gameAccount?->game?->iconUrl())
                        <img src="{{ $order->gameAccount->game->iconUrl() }}" alt="{{ $order->gameAccount->game->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-3xl">sports_esports</span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-label-mono text-label-mono text-outline">{{ $order->order_no }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                            @if($order->status->value === 'completed') bg-tertiary-container/30 text-tertiary
                            @elseif(in_array($order->status->value, ['escrow','handover'])) bg-secondary-container/30 text-secondary
                            @elseif(in_array($order->status->value, ['disputed','buyer_confirmation'])) bg-error-container/30 text-error
                            @else bg-surface-container text-on-surface-variant @endif">
                            {{ $order->status->label() }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge text-[10px]">{{ $order->payment_method_label }}</span>
                    </div>
                    <h3 class="font-body-md text-body-md font-semibold text-on-surface mt-1 line-clamp-1">{{ $order->gameAccount?->title ?? $order->items()->first()?->account_title }}</h3>
                    <p class="font-label-stat text-label-stat text-on-surface-variant mt-0.5">
                        Buyer <strong class="text-on-surface">{{ $order->buyer?->username }}</strong>
                        · Seller <strong class="text-on-surface">{{ $order->seller?->username }}</strong>
                        · {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                    </p>
                    @if($order->payment_due_at && $order->status->value === 'pending_payment')
                        <p class="font-label-stat text-label-stat text-error mt-0.5">Batas bayar: <span data-countdown="{{ $order->payment_due_at?->timestamp }}">{{ $order->payment_due_at->diffForHumans() }}</span></p>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    <div class="font-headline-sm text-headline-sm font-bold text-tertiary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    <a href="{{ route('orders.show', $order) }}" class="mt-2 inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-secondary-container/40 text-secondary font-body-sm text-body-sm font-semibold hover:bg-secondary hover:text-on-secondary transition-colors">
                        <span class="material-symbols-outlined text-base">visibility</span>Buka Order
                    </a>
                </div>
            </article>
        @empty
            <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                <span class="material-symbols-outlined text-6xl text-outline">receipt_long</span>
                <p class="font-body-md text-body-md text-on-surface-variant mt-3">Tidak ada order pada filter ini.</p>
            </div>
        @endforelse

        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
@endsection