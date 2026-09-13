@extends('layouts.app')

@section('title', 'Pembayaran · ' . $order->order_no)

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        <div class="grid lg:grid-cols-5 gap-8 items-start">
            <div class="lg:col-span-3 space-y-6">
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div>
                            <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Pesanan</span>
                            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-0.5">{{ $order->order_no }}</h1>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            <span class="px-3 py-1 rounded-full bg-secondary-container/30 text-secondary font-badge text-badge font-semibold">{{ $order->status->label() }}</span>
                            <span data-countdown="{{ $order->payment_due_at?->timestamp }}" class="font-label-mono text-label-mono text-error font-semibold">Selesaikan sebelum batas waktu</span>
                        </div>
                    </div>

                    @php
                        $isWallet = $order->payment_method === 'wallet';
                        $bankVaNumber = '8800' . str_pad((string) ($order->id * 7919 % 100000000), 8, '0', STR_PAD_LEFT);
                        $ewalletNumber = '0812' . str_pad((string) ($order->id * 3571 % 10000000), 8, '0', STR_PAD_LEFT);
                    @endphp

                    @if($isWallet)
                        <div class="rounded-2xl bg-primary-container/15 border border-primary/40 p-6 text-center">
                            <span class="material-symbols-outlined text-[44px] text-tertiary">savings</span>
                            <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-2">Bayar dengan Saldo Rekber</h2>
                            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Transaksi ini bebas biaya escrow. Dana akan langsung dikunci di Escrow Vault.</p>
                            <div class="mt-5 font-headline-xl text-headline-xl font-bold text-tertiary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            <form method="POST" action="{{ route('checkout.mark-paid', $order) }}" class="mt-6 inline-flex">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold px-6 py-3.5 hover:bg-primary transition-all">
                                    <span class="material-symbols-outlined text-lg">lock</span>Konfirmasi Bayar dengan Saldo
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-6">
                            <div class="rounded-2xl bg-surface-container p-6">
                                <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Instruksi Pembayaran</span>
                                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-1">{{ $order->payment_method_label }}</h2>
                                <div class="flex items-center gap-4 mt-4">
                                    <div class="w-24 h-24 rounded-2xl bg-surface-container bg-white p-1.5 shrink-0">
                                        <svg viewBox="0 0 96 96" class="w-full h-full">
                                            <rect x="10" y="10" width="76" height="76" rx="8" fill="#0d0d14" stroke="#2a2a3a" stroke-width="4"/>
                                            @for($y = 0; $y < 8; $y++)
                                                @for($x = 0; $x < 8; $x++)
                                                    @if((($x * 7 + $y * 13 + $order->id) % 3) === 0)
                                                        <rect x="{{ 17 + $x * 10 }}" y="{{ 17 + $y * 10 }}" width="6" height="6" fill="#c8b6ff"/>
                                                    @endif
                                                @endfor
                                            @endfor
                                            <rect x="14" y="14" width="20" height="20" fill="#0d0d14" stroke="#c8b6ff" stroke-width="3"/>
                                            <rect x="62" y="14" width="20" height="20" fill="#0d0d14" stroke="#c8b6ff" stroke-width="3"/>
                                            <rect x="14" y="62" width="20" height="20" fill="#0d0d14" stroke="#c8b6ff" stroke-width="3"/>
                                        </svg>
                                    </div>
                                    <ol class="space-y-1.5 font-body-sm text-body-sm text-on-surface-variant list-decimal list-inside">
                                        <li><strong class="text-on-surface">{{ $order->payment_method === 'qris' ? 'Scan QR' : 'Kirim pembayaran ke' }}:</strong>
                                            <span class="font-label-mono text-label-mono text-tertiary">{{ $order->payment_method === 'qris' ? 'QRIS GameVault' : ($order->payment_method === 'bank_va' ? $bankVaNumber : ($order->payment_method === 'ewallet' ? $ewalletNumber : '8881 0123 4567')) }}</span>
                                        </li>
                                        <li>Nominal: <strong class="font-label-mono text-on-surface">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></li>
                                        <li>Transfer <strong class="text-on-surface">tanpa kode unik tambahan</strong> — sistem mencocokkan otomatis.</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-primary-container/15 border border-primary/40 p-6 text-center">
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Sudah transfer? Klik tombol di bawah. Konfirmasi otomatis dalam <strong class="text-on-surface">&lt; 1 menit</strong> (demo simulasi).</p>
                                <form method="POST" action="{{ route('checkout.mark-paid', $order) }}" class="mt-4">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold px-6 py-3.5 hover:bg-primary transition-all">
                                        <span class="material-symbols-outlined text-lg">verified</span>Saya Sudah Bayar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($errors->checkout->any())
                        <div class="mt-5 rounded-2xl bg-error-container/20 border border-error/30 px-4 py-3 text-error font-body-sm text-body-sm">{{ $errors->checkout->first() }}</div>
                    @endif
                </div>
            </div>

            <aside class="lg:col-span-2 lg:sticky top-36 space-y-6">
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Rincian</span>
                    <div class="mt-4 space-y-2.5 font-body-md text-body-md">
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Akun</span>
                            <span class="text-on-surface text-right w-1/2 line-clamp-2">{{ $order->gameAccount?->title }}</span>
                        </div>
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Subtotal</span>
                            <span class="font-label-mono text-on-surface">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-tertiary">
                                <span>Diskon</span>
                                <span class="font-label-mono">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Biaya Escrow</span>
                            <span class="font-label-mono text-on-surface">{{ $order->fee_amount > 0 ? 'Rp ' . number_format($order->fee_amount, 0, ',', '.') : 'Rp 0' }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-outline-variant/30">
                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">Total</span>
                            <span class="font-headline-sm text-headline-sm font-bold text-tertiary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Butuh bantuan?</span>
                    <ul class="mt-3 space-y-2.5 font-body-sm text-body-sm text-on-surface-variant">
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary text-lg">support_agent</span>Live chat rekber 24/7</li>
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary text-lg">schedule</span>Pesanan otomatis batal jika tidak dibayar sesuai tenggat</li>
                        <li class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary text-lg">shield</span>Dana aman di escrow, bukan ke seller langsung</li>
                    </ul>
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-4" onsubmit="return confirm('Yakin batalkan pesanan ini?')">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant/40 text-on-surface-variant font-body-sm text-body-sm font-semibold py-2.5 hover:bg-error-container/20 hover:text-error transition-colors">
                            <span class="material-symbols-outlined text-lg">close</span>Batalkan Pesanan
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
@endsection