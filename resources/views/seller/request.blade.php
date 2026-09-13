@extends('layouts.app')

@section('title', 'Jadi Penjual · GameVault')

@section('content')
    <div class="max-w-[900px] mx-auto px-6 lg:px-8 py-10">
        @if(session('error'))
            <div class="mb-6 rounded-2xl bg-error-container/20 border border-error/30 px-4 py-3 text-error font-body-sm text-body-sm">{{ session('error') }}</div>
        @endif

        @if(($alreadySeller ?? false))
            <div class="rounded-3xl bg-tertiary-container/15 border border-tertiary/40 p-10 text-center">
                <span class="material-symbols-outlined text-6xl text-tertiary">storefront</span>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-4">Kamu sudah menjadi seller!</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Toko kamu aktif. Kelola toko dari Seller Center.</p>
                <div class="flex justify-center gap-3 mt-6">
                    <a href="{{ route('seller.dashboard') }}" class="px-6 py-3 rounded-2xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">Buka Seller Center</a>
                    <a href="{{ route('seller.accounts.create') }}" class="px-6 py-3 rounded-2xl bg-secondary-container/30 text-secondary font-body-sm text-body-sm font-semibold hover:bg-secondary hover:text-on-secondary transition-colors">Jual Akun Baru</a>
                </div>
            </div>
        @elseif(($request ?? null)?->status->value === 'pending')
            <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-10 text-center">
                <span class="material-symbols-outlined text-6xl text-secondary">hourglass_top</span>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mt-4">Pengajuan Sedang Direview</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Toko <strong class="text-on-surface">"{{ $request->store_name }}"</strong> sedang direview tim admin. Biasanya selesai dalam 1×24 jam.</p>
                <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary-container/30 text-secondary font-badge text-badge font-semibold">
                    <span class="material-symbols-outlined text-sm">schedule</span>Status: Menunggu Review
                </div>
            </div>
        @else
            <div class="mb-8 text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-container/25 text-secondary font-badge text-badge font-semibold">
                    <span class="material-symbols-outlined text-sm">verified_user</span>100% GARANSI REKBER
                </span>
                <h1 class="font-headline-xl text-headline-xl font-bold text-on-surface mt-4">Jadi Penjual GameVault</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2 max-w-lg mx-auto">Jual akun game kamu dengan aman — dana dijamin escrow, pembeli diverifikasi, sistem rekber otomatis.</p>
            </div>

            <div class="grid gap-4 grid-cols-3 mb-8">
                @php
                    $benefits = [
                        ['icon' => 'shield', 'title' => 'Dana Dijamin Escrow', 'desc' => 'Pembayaran otomatis dikunci dan dirilis hanya setelah buyer konfirmasi.'],
                        ['icon' => 'auto_awesome', 'title' => 'Up to 5% Fee', 'desc' => 'Biaya transaksi ringan, maksimal Rp 1.000.000. Kirim kredit tanpa batas.'],
                        ['icon' => 'bolt', 'title' => 'Live dalam 24 Jam', 'desc' => 'List akun setelah review admin cepat — approval rata-rata 1 hari.'],
                    ];
                @endphp
                @foreach($benefits as $b)
                    <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                        <span class="material-symbols-outlined text-secondary text-3xl">{{ $b['icon'] }}</span>
                        <h3 class="font-body-md text-body-md font-bold text-on-surface mt-3">{{ $b['title'] }}</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $b['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('seller.request.submit') }}" class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-7 space-y-5">
                @csrf
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Nama Toko</label>
                    <input name="store_name" value="{{ old('store_name') }}" required maxlength="60" placeholder="Ex: Renata Official Game Store" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                    @error('store_name') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Alasan & Komitmen Anda</label>
                    <textarea name="reason" required rows="3" maxlength="500" placeholder="Ceritakan kenapa ingin jadi penjual & bagaimana menjaga reputasi toko" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none"></textarea>
                    @error('reason') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Pengalaman Jual Akun <span class="text-outline">(opsional)</span></label>
                    <textarea name="experience" rows="2" maxlength="500" placeholder="Ex: sudah lebih dari 20 transaksi sukses di komunitas MLBB" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none"></textarea>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-tertiary-container/15 border border-tertiary/30 p-4">
                    <span class="material-symbols-outlined text-tertiary">verified_user</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Dengan mengirim pengajuan, kamu menyetujui <strong class="text-on-surface">Peraturan Penjual GameVault</strong> — penjualan akun curian / ban / hacker dapat mengakibatkan pembekuan akun.</p>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold py-3.5 hover:bg-primary transition-colors">Kirim Pengajuan Jadi Penjual</button>
            </form>
        @endif
    </div>
@endsection