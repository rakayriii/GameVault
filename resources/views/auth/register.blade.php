@extends('layouts.app')

@section('title', 'Daftar Akun - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-tertiary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-8 md:py-12 relative z-10">
            <nav class="flex flex-wrap items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">home</span>Home
                </a>
                <span class="material-symbols-outlined text-xs text-outline">chevron_right</span>
                <span class="text-secondary font-medium">Daftar Akun GameVault</span>
            </nav>

            <div class="flex flex-col gap-4 mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-high text-secondary font-label-stat text-label-stat tracking-wider w-fit">
                    <span class="inline-block w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                    <span>ENKRIPSI HARDWARE END-TO-END / 2FA / ANTI-BRUTEFORCE</span>
                </div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold">
                    Buat Akun <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-tertiary">Gamers</span> Baru
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                    Daftar gratis, langsung bisa transaksi akun game dengan jaminan rekber, escrow, dan garansi anti-hackback 100%.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start max-w-5xl">
                <form method="POST" action="{{ route('register') }}" class="bg-surface-container-low/95 backdrop-blur-xl rounded-2xl shadow-xl p-6 sm:p-8 flex flex-col gap-5">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">Nama Lengkap</label>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">badge</span>
                            <input name="name" value="{{ old('name') }}" required autofocus class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-medium" placeholder="Nama lengkap kamu">
                        </div>
                        @error('name') <p class="font-label-stat text-label-stat text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">Username / Gamer Tag</label>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">account_circle</span>
                            <input name="username" value="{{ old('username') }}" required pattern="[a-zA-Z0-9._]+" class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-medium font-mono" placeholder="cth: valkyrie_pro">
                        </div>
                        @error('username') <p class="font-label-stat text-label-stat text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">Alamat Email</label>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">mail</span>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-medium" placeholder="email@contoh.com">
                        </div>
                        @error('email') <p class="font-label-stat text-label-stat text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">No. WhatsApp <span class="font-label-stat text-label-stat text-outline">(opsional, untuk verifikasi seller)</span></label>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">phone_iphone</span>
                            <input name="phone" value="{{ old('phone') }}" class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-medium" placeholder="62 812 3456 7890">
                        </div>
                        @error('phone') <p class="font-label-stat text-label-stat text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Password Enkripsi</label>
                        </div>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">lock</span>
                            <input type="password" name="password" required class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-mono" placeholder="Minimal 8 karakter unik" data-password-toggle>
                        </div>
                        @error('password') <p class="font-label-stat text-label-stat text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">Konfirmasi Password</label>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">lock_reset</span>
                            <input type="password" name="password_confirmation" required class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-mono" placeholder="Ulangi password kamu">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl bg-surface-container">
                        <input type="checkbox" name="two_factor" value="1" class="h-4 w-4 rounded accent-tertiary">
                        <span class="flex flex-col">
                            <span class="font-body-sm text-body-sm text-on-surface font-semibold">Aktifkan 2FA (Recommended)</span>
                            <span class="font-label-stat text-label-stat text-on-surface-variant">Lindungi akunmu dengan Authenticator App &amp; kode pemulihan.</span>
                        </span>
                    </label>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center justify-center gap-2 hover:bg-primary transition-all shadow-[0_4px_20px_rgba(160,120,255,0.35)]">
                        <span class="material-symbols-outlined text-lg">how_to_reg</span>
                        <span>Buat Akun &amp; Daftar</span>
                    </button>

                    <p class="text-center font-body-sm text-body-sm text-on-surface-variant">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-primary hover:underline font-semibold">Masuk di sini</a>
                    </p>
                </form>

                <div class="flex flex-col gap-4">
                    <div class="bg-surface-container-low/95 rounded-2xl p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-secondary-container/20 text-secondary flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">shield</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Keuntungan Daftar Gratis</span>
                                <span class="font-label-stat text-label-stat text-tertiary uppercase">0 biaya • tanpa kartu kredit</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach([
                                ['icon' => 'verified_user', 'title' => 'Rekber Otomatis', 'desc' => 'Dana ditahan aman escrow GameVault.'],
                                ['icon' => 'history_edu', 'title' => 'Garansi Hackback', 'desc' => 'Proteksi 30 hari di setiap transaksi.'],
                                ['icon' => 'bolt', 'title' => 'Handover Instan', 'desc' => 'Terima akun dalam waktu &lt; 10 menit.'],
                                ['icon' => 'account_balance_wallet', 'title' => 'Wallet Rekber', 'desc' => 'Top up & tarik dana kapan saja.'],
                            ] as $benefit)
                                <div class="p-3.5 rounded-xl bg-surface-container flex flex-col gap-1.5">
                                    <span class="material-symbols-outlined text-xl text-primary">{{ $benefit['icon'] }}</span>
                                    <span class="font-headline-sm text-sm font-bold text-on-surface">{{ $benefit['title'] }}</span>
                                    <span class="font-body-sm text-[12px] text-on-surface-variant">{{ $benefit['desc'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-surface-container-lowest flex items-center gap-3 border border-outline-variant/20">
                        <span class="material-symbols-outlined text-secondary text-xl shrink-0">gshield</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Setelah mendaftar, kamu dapat langsung menjadi buyer. Ingin jual akun? Lengkapi verifikasi seller via <span class="text-on-surface font-semibold">Jadi Penjual</span>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection