@extends('layouts.app')

@section('title', 'Checkout · ' . $gameAccount->title)

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('marketplace.index') }}" class="hover:text-secondary transition-colors">Marketplace</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <a href="{{ route('marketplace.show', $gameAccount) }}" class="hover:text-secondary transition-colors line-clamp-1">{{ $gameAccount->title }}</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Checkout</span>
        </nav>

        <div class="mb-8">
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface flex items-center gap-3">
                Checkout Aman
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-tertiary-container/25 text-tertiary font-badge text-badge font-semibold">
                    <span class="material-symbols-outlined text-sm">verified_user</span>GARANSI REKBER
                </span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Transaksi dilindungi escrow GameVault sampai kamu berhasil mengubah email & password akun.</p>
        </div>

        <form method="POST" action="{{ route('checkout.store', $gameAccount) }}" x-data="{ method: 'qris', agree: false }" class="grid lg:grid-cols-5 gap-8 items-start">
            @csrf
            @if($errors->checkout->any())
                <div class="col-span-full rounded-2xl bg-error-container/20 border border-error/30 px-4 py-3 text-error font-body-sm text-body-sm">
                    {{ $errors->store->first() ?? $errors->checkout->first() }}
                </div>
            @endif

            <div class="lg:col-span-3 space-y-6">
                <section class="rounded-3xl bg-surface-container-low p-6 border border-outline-variant/20">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>Pilih Metode Pembayaran
                    </h2>

                    <div class="grid sm:grid-cols-2 gap-3">
                        @php
                            $methods = [
                                ['id' => 'qris', 'label' => 'QRIS Realtime', 'desc' => 'Scan QR — proses otomatis', 'icon' => 'qr_code_2', 'fee' => 'Fee escrow 5%'],
                                ['id' => 'bank_va', 'label' => 'Virtual Account', 'desc' => 'ATM / e-banking 24 jam', 'icon' => 'account_balance', 'fee' => 'Fee escrow 5%'],
                                ['id' => 'ewallet', 'label' => 'Dompet Digital', 'desc' => 'OVO, GoPay, DANA, ShopeePay', 'icon' => 'smartphone', 'fee' => 'Fee escrow 5%'],
                                ['id' => 'bank_transfer', 'label' => 'Transfer Bank', 'desc' => 'Manual konfirmasi', 'icon' => 'payments', 'fee' => 'Fee escrow 5%'],
                                ['id' => 'wallet', 'label' => 'Saldo Rekber', 'desc' => 'Pakai saldo wallet — tanpa fee', 'icon' => 'savings', 'fee' => 'FREE FEE'],
                            ];
                        @endphp

                        @foreach($methods as $m)
                            <label @class([
                                'relative flex flex-col gap-0.5 rounded-2xl border p-4 cursor-pointer transition-all select-none',
                                'bg-primary-container/20 border-primary/60 ring-1 ring-primary/40',
                                'bg-surface-container-high border-outline-variant/30 hover:border-outline',
                            ]) :class="{ '!bg-primary-container/20 !border-primary/60 ring-1 ring-primary/40': method === '{{ $m['id'] }}' }">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="{{ $m['id'] }}"
                                    class="peer hidden"
                                    x-model="method"
                                    :checked="method === '{{ $m['id'] }}'"
                                >
                                <div class="flex items-start justify-between gap-3">
                                    <span class="material-symbols-outlined text-secondary text-2xl">{{ $m['icon'] }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase"
                                          :class="method === '{{ $m['id'] }}' ? 'bg-tertiary-container/40 text-tertiary' : 'bg-surface-container text-on-surface-variant'">{{ $m['fee'] }}</span>
                                </div>
                                <span class="font-body-sm text-body-sm font-semibold text-on-surface mt-1">{{ $m['label'] }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $m['desc'] }}</span>
                                @if($m['id'] === 'wallet')
                                    <span class="font-label-mono text-label-mono text-tertiary mt-1">Saldo: {{ $wallet ? 'Rp ' . number_format($wallet->available_balance, 0, ',', '.') : 'Rp 0' }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-3xl bg-surface-container-low p-6 border border-outline-variant/20">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-tertiary">lock</span>Protokol Escrow
                    </h2>
                    <ol class="space-y-2 font-body-md text-body-md text-on-surface-variant list-decimal list-inside marker:text-secondary">
                        <li>Dana kamu <strong class="text-on-surface">dikunci di Escrow Vault</strong> GameVault setelah pembayaran.</li>
                        <li>Seller menyerahkan login akun via ruang handover rahasia.</li>
                        <li>Kamu verifikasi akun & ganti email/password, lalu konfirmasi.</li>
                        <li>Seller baru menerima dana setelah kamu konfirmasi. <strong class="text-on-surface">100% anti-hackback.</strong></li>
                    </ol>
                    <label class="mt-5 flex items-start gap-3 rounded-2xl bg-surface-container p-4 cursor-pointer">
                        <input type="checkbox" name="agree_escrow" value="1" class="mt-0.5 h-5 w-5 rounded-md accent-primary" x-model="agree">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Saya memahami dan menyetujui <strong class="text-on-surface">Protokol Escrow GameVault</strong> serta kebijakan garansi uang kembali 100%.
                        </span>
                    </label>
                </section>
            </div>

            <aside class="lg:col-span-2 lg:sticky top-36 space-y-6">
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 overflow-hidden">
                    <div class="p-5 border-b border-outline-variant/20">
                        <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Ringkasan Pesanan</span>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex gap-3">
                            <div class="w-14 h-14 rounded-xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0">
                                <x-game-icon :game="$gameAccount->game" size="w-14 h-14" icon="text-2xl" />
                            </div>
                            <div class="min-w-0">
                                <p class="font-body-md text-body-md font-semibold text-on-surface line-clamp-2">{{ $gameAccount->title }}</p>
                                <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $gameAccount->game?->name }} · {{ $gameAccount->server ?: 'Semua Server' }}</p>
                            </div>
                        </div>

                        <div class="h-px bg-outline-variant/30"></div>

                        <div class="space-y-2.5 font-body-md text-body-md">
                            <div class="flex justify-between text-on-surface-variant">
                                <span>Harga Akun
                                    @if($gameAccount->strike_price)
                                        <span class="text-outline line-through ml-1 font-label-mono text-label-stat">Rp {{ number_format($gameAccount->strike_price, 0, ',', '.') }}</span>
                                    @endif
                                </span>
                                <span class="font-label-mono text-on-surface">Rp {{ number_format($gameAccount->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-on-surface-variant">
                                <span>Biaya Escrow</span>
                                <span class="font-label-mono" x-text="method === 'wallet' ? 'Rp 0 (FREE)' : '5% · maks Rp 1.000.000'">5% · maks Rp 1.000.000</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-outline-variant/30">
                                <span class="font-headline-sm text-headline-sm font-bold text-on-surface">Total</span>
                                <span class="font-headline-sm text-headline-sm font-bold text-tertiary" x-text="method === 'wallet' ? '{{ 'Rp ' . number_format($gameAccount->price, 0, ',', '.') }}' : '{{ 'Rp ' . number_format($gameAccount->price + min((int) round($gameAccount->price * 0.05), 1000000), 0, ',', '.') }}'">Rp {{ number_format($gameAccount->price + min((int) round($gameAccount->price * 0.05), 1000000), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-surface-container pt-4">
                        <button type="submit" :disabled="!agree" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold py-3.5 hover:bg-primary transition-all shadow-[0_6px_20px_rgba(160,120,255,0.45)] disabled:opacity-40 disabled:cursor-not-allowed">
                            <span class="material-symbols-outlined text-lg">lock_open</span>
                            <span>Buat Pesanan & Melanjutkan</span>
                        </button>
                        <p class="mt-3 text-center font-label-stat text-label-stat text-outline" x-show="!agree">Tandai persetujuan protokol escrow untuk melanjutkan</p>
                    </div>
                </div>

                <div class="rounded-3xl bg-tertiary-container/15 border border-tertiary/30 p-5 flex gap-3">
                    <span class="material-symbols-outlined text-tertiary text-2xl shrink-0">shield</span>
                    <div class="font-body-sm text-body-sm text-on-surface-variant">
                        <strong class="text-on-surface">Garansi Uang Kembali 100%.</strong>
                        Karena dana disimpan di escrow, kamu tidak akan kehilangan uang selama belum memverifikasi akun.
                    </div>
                </div>
            </aside>
        </form>
    </div>
@endsection