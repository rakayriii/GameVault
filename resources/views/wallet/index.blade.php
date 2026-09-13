@extends('layouts.app')

@section('title', 'Wallet Rekber · GameVault')

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Wallet Rekber</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Simpan saldo untuk transaksi bebas fee & tarik hasil penjualan.</p>
        </div>

        @if(session('error'))
            <div class="mb-6 rounded-2xl bg-error-container/20 border border-error/30 px-4 py-3 text-error font-body-sm text-body-sm">{{ session('error') }}</div>
        @endif

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6 md:col-span-2">
                <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Saldo Tersedia</span>
                <div class="mt-2 font-headline-xl text-headline-xl font-bold text-on-surface">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}</div>
                <div class="flex flex-wrap gap-4 mt-4 font-label-stat text-label-stat text-on-surface-variant">
                    <span>Total Diterima: <strong class="text-on-surface font-label-mono">Rp {{ number_format($wallet->transactions()->where('direction', 'in')->where('status', 'success')->sum('amount'), 0, ',', '.') }}</strong></span>
                    <span>Dana dikunci escrow: <strong class="text-secondary font-label-mono">Rp {{ number_format($wallet->escrow_balance, 0, ',', '.') }}</strong></span>
                </div>
            </div>
            <div class="rounded-3xl bg-tertiary-container/15 border border-tertiary/40 p-6 flex flex-col justify-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-tertiary">auto_awesome</span>
                    <span class="font-label-stat text-label-stat text-tertiary uppercase tracking-wider">Bebas Fee</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">Bayar dengan saldo = <strong class="text-on-surface">0% biaya escrow</strong>. Penarikan mulai Rp 50.000.</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6 items-start mb-10">
            <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6" x-data="{ amount: 100000, method: 'qris' }">
                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">add_card</span>Isi Saldo
                </h2>
                <form method="POST" action="{{ route('wallet.deposit') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Nominal</label>
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach([50000, 100000, 250000, 500000, 1000000] as $v)
                                <button type="button" @click="amount = {{ $v }}" class="px-3 py-1.5 rounded-xl font-label-mono text-label-mono text-on-surface-variant bg-surface-container-high hover:text-tertiary transition-colors" :class="amount === {{ $v }} ? '!bg-primary-container/40 !text-tertiary' : ''">Rp {{ number_format($v, 0, ',', '.') }}</button>
                            @endforeach
                        </div>
                        <input type="number" name="amount" x-model="amount" min="10000" required class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Metode</label>
                        <select name="method" x-model="method" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
                            <option value="qris">QRIS</option>
                            <option value="bank_va">Virtual Account</option>
                            <option value="ewallet">Dompet Digital</option>
                            <option value="bank_transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-2xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold py-3 hover:bg-primary transition-colors">Isi Sekarang</button>
                </form>
            </div>

            <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6" x-data="{ amount: '', account: '' }">
                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">outbox</span>Tarik Dana
                </h2>
                <form method="POST" action="{{ route('wallet.withdraw') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Nominal (fee 1% maks Rp 25.000)</label>
                        <input type="number" name="amount" min="50000" required placeholder="Min. Rp 50.000" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Tujuan</label>
                        @forelse($payoutAccounts as $acc)
                            <label class="flex items-center gap-3 rounded-xl bg-surface-container p-3 mb-2 cursor-pointer">
                                <input type="radio" name="payout_account_id" value="{{ $acc->id }}" class="accent-secondary" required @if($acc->is_primary) checked @endif>
                                <span class="font-body-sm text-body-sm">
                                    <span class="text-on-surface font-semibold block">{{ $acc->bank_name }}</span>
                                    <span class="text-on-surface-variant font-label-mono">•••• {{ substr($acc->account_number, -4) }} · {{ $acc->account_name }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Belum ada rekening. Tambahkan di bawah dulu.</p>
                        @endforelse
                    </div>
                    @if(!auth()->user()->two_factor_enabled)
                        <div class="rounded-xl bg-error-container/15 border border-error/30 px-3 py-2.5 text-error font-body-sm text-body-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">gpp_bad</span>2FA wajib aktif sebelum tarik dana.
                        </div>
                    @endif
                    <button type="submit" class="w-full rounded-2xl bg-secondary-container/40 text-secondary font-body-sm text-body-sm font-semibold py-3 hover:bg-secondary hover:text-on-secondary transition-colors">Tarik Dana</button>
                </form>
                <details class="mt-4 rounded-2xl bg-surface-container p-4">
                    <summary class="cursor-pointer font-body-sm text-body-sm text-secondary font-semibold">+ Tambah rekening tujuan</summary>
                    <form method="POST" action="{{ route('wallet.payout-account') }}" class="mt-3 space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <input name="bank_name" placeholder="Bank (ex: BCA)" required class="col-span-2 rounded-xl bg-surface-container-high border border-outline-variant/30 px-3 py-2.5 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                            <input name="account_number" placeholder="No. rekening" required class="rounded-xl bg-surface-container-high border border-outline-variant/30 px-3 py-2.5 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                            <input name="account_name" placeholder="Nama pemilik" required class="rounded-xl bg-surface-container-high border border-outline-variant/30 px-3 py-2.5 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold py-2.5 hover:bg-primary transition-colors">Simpan Rekening</button>
                    </form>
                </details>
            </div>

            <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6">
                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">shield</span>Keamanan
                </h2>
                <ul class="mt-4 space-y-3 font-body-sm text-body-sm text-on-surface-variant">
                    <li class="flex gap-2.5"><span class="material-symbols-outlined text-secondary text-lg shrink-0">verified_user</span>{{ auth()->user()->two_factor_enabled ? '2FA aktif — penarikan aman.' : 'Aktifkan 2FA di pengaturan akun untuk bisa menarik dana.' }}</li>
                    <li class="flex gap-2.5"><span class="material-symbols-outlined text-secondary text-lg shrink-0">lock</span>Penarikan selalu diverifikasi manual oleh tim GameVault.</li>
                </ul>
                <a href="{{ route('wallet.withdrawals') }}" class="mt-4 inline-flex items-center gap-1.5 text-secondary font-body-sm text-body-sm font-semibold hover:text-tertiary transition-colors">
                    Riwayat penarikan <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>

        <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20">
            <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center justify-between">
                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Riwayat Transaksi</h2>
                <span class="font-label-mono text-label-mono text-outline">{{ $wallet->transactions()->count() }} transaksi</span>
            </div>
            <div class="divide-y divide-outline-variant/10">
                @forelse($transactions as $t)
                    <div class="px-6 py-4 flex items-center gap-4">
                        <span class="w-11 h-11 rounded-2xl flex items-center justify-center {{ $t->direction === 'in' ? 'bg-tertiary-container/25 text-tertiary' : 'bg-secondary-container/25 text-secondary' }}">
                            <span class="material-symbols-outlined">{{ $t->type->value === 'deposit' ? 'south_west' : ($t->type->value === 'withdrawal' ? 'north_east' : 'lock') }}</span>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $t->description }}</p>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">{{ ucfirst(str_replace('_', ' ', $t->type->value)) }} · {{ $t->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-label-mono text-label-mono font-semibold {{ $t->direction === 'in' ? 'text-tertiary' : 'text-on-surface' }}">
                                {{ $t->direction === 'in' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </span>
                            <span class="block font-label-stat text-label-stat {{ $t->status->value === 'success' ? 'text-tertiary' : ($t->status->value === 'failed' ? 'text-error' : 'text-outline') }}">
                                {{ ucfirst($t->status->value) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <span class="material-symbols-outlined text-5xl text-outline">account_balance_wallet</span>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-2">Belum ada transaksi.</p>
                    </div>
                @endforelse
            </div>
            <div class="px-6 py-4 border-t border-outline-variant/20">{{ $transactions->links() }}</div>
        </section>
    </div>
@endsection