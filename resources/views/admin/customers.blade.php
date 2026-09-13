@extends('layouts.app')

@section('title', 'Customers · Admin')

@section('content')
    <div class="max-w-[1220px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Pelanggan</span>
        </nav>

        <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Pelanggan</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Semua user, saldo wallet, dan status verifikasi.</p>
            </div>
            <form method="GET" action="{{ route('admin.customers') }}" class="flex items-end gap-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                    <input name="q" value="{{ request('q') }}" placeholder="Cari username / email / nama..." class="w-72 rounded-xl bg-surface-container border border-outline-variant/30 pl-10 pr-3 py-2.5 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">Cari</button>
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">
            @php
                $cards = [
                    ['label' => 'Total User', 'value' => (string) $summary['total'], 'icon' => 'group'],
                    ['label' => 'Buyer', 'value' => (string) $summary['buyers'], 'icon' => 'person'],
                    ['label' => 'Seller', 'value' => (string) $summary['sellers'], 'icon' => 'storefront'],
                    ['label' => 'Verified', 'value' => (string) $summary['verified'], 'icon' => 'verified_user', 'accent' => 'text-tertiary'],
                    ['label' => 'Wallet Aktif', 'value' => (string) $summary['wallets'], 'icon' => 'account_balance_wallet'],
                    ['label' => 'Total Saldo', 'value' => 'Rp ' . number_format($summary['total_balance'], 0, ',', '.'), 'icon' => 'savings', 'accent' => 'text-tertiary'],
                    ['label' => 'Escrow Terkunci', 'value' => 'Rp ' . number_format($summary['total_escrow'], 0, ',', '.'), 'icon' => 'lock', 'accent' => 'text-secondary'],
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

        <div class="flex gap-2 overflow-x-auto pb-2 mb-6">
            @php
                $tabs = ['' => 'Semua', 'buyer' => 'Buyer', 'seller' => 'Seller', 'arbitrator' => 'Tim Rekber', 'admin' => 'Admin'];
            @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('admin.customers', array_filter(['role' => $val, 'q' => request('q')])) }}"
                   class="shrink-0 px-4 py-2 rounded-full font-body-sm text-body-sm font-semibold transition-colors {{ $role === $val ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container text-on-surface-variant hover:text-on-surface' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @forelse($users as $user)
            <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 mb-4 flex flex-wrap items-center gap-4">
                <x-avatar :user="$user" class="w-12 h-12 rounded-2xl"/>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-body-md text-body-md font-bold text-on-surface">{{ $user->username }}</h3>
                        @if($user->is_verified)
                            <span class="material-symbols-outlined text-secondary text-sm">verified</span>
                        @endif
                        <span class="px-2.5 py-0.5 rounded-full font-badge text-badge text-[10px] font-semibold
                            {{ $user->role->value === 'seller' ? 'bg-tertiary-container/30 text-tertiary' : ($user->isStaff() ? ($user->role->value === 'admin' ? 'bg-primary-container/30 text-primary' : 'bg-secondary-container/30 text-secondary') : 'bg-surface-container text-on-surface-variant') }}">
                            {{ $user->role_label }}
                        </span>
                        @if($user->sellerProfile?->store_name)
                            <span class="font-label-stat text-label-stat text-on-surface-variant">{{ $user->sellerProfile->store_name }}</span>
                        @endif
                    </div>
                    <p class="font-label-stat text-label-stat text-on-surface-variant mt-0.5">{{ $user->name }} · {{ $user->email }} · Bergabung {{ $user->created_at->translatedFormat('d M Y') }}</p>
                </div>
                <div class="text-right shrink-0 space-y-0.5">
                    <div class="font-label-mono text-label-mono text-tertiary">Saldo: Rp {{ number_format($user->wallet?->available_balance ?? 0, 0, ',', '.') }}</div>
                    <div class="font-label-mono text-label-mono text-secondary">Escrow: Rp {{ number_format($user->wallet?->escrow_balance ?? 0, 0, ',', '.') }}</div>
                    <div class="font-label-stat text-label-stat text-outline">{{ $user->orders()->count() }} pesanan · {{ $user->sales()->count() }} penjualan</div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                <span class="material-symbols-outlined text-6xl text-outline">group_off</span>
                <p class="font-body-md text-body-md text-on-surface-variant mt-3">Tidak ada user pada filter ini.</p>
            </div>
        @endforelse

        <div class="mt-6">{{ $users->links() }}</div>
    </div>
@endsection