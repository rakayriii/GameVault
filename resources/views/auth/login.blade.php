@extends('layouts.app')

@section('title', 'Masuk - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 right-10 w-96 h-96 bg-secondary-container/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-8 md:py-12 relative z-10">
            <nav class="flex flex-wrap items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">home</span>Home
                </a>
                <span class="material-symbols-outlined text-xs text-outline">chevron_right</span>
                <a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Keamanan Akun</a>
                <span class="material-symbols-outlined text-xs text-outline">chevron_right</span>
                <span class="text-secondary font-medium">Masuk &amp; Registrasi Terproteksi</span>
            </nav>

            <div class="flex flex-col gap-4 mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-high text-secondary font-label-stat text-label-stat tracking-wider w-fit">
                    <span class="inline-block w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                    <span>ENKRIPSI HARDWARE END-TO-END / 2FA LEVEL BANK / ANTI-BRUTEFORCE</span>
                </div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold">
                    Pusat Akses Aman &amp; Otentikasi <span class="text-primary">Gamers</span>
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                    Masuk ke akun GameVault untuk transaksi jual-beli akun game dengan perlindungan saldo rekber dan brankas terenkripsi tingkat tinggi.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start max-w-5xl">
                <form method="POST" action="{{ route('login') }}" class="bg-surface-container-low/95 backdrop-blur-xl rounded-2xl shadow-xl p-6 sm:p-8 flex flex-col gap-5">
                    @csrf
                    @if(session('status'))
                        <div class="p-3 rounded-xl bg-tertiary-container/20 text-tertiary font-body-sm text-body-sm">{{ session('status') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="p-3 rounded-xl bg-error-container/20 text-error font-body-sm text-body-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">error</span>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">Alamat Email</label>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">account_circle</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-medium" placeholder="Masukkan email kamu">
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Password Enkripsi</label>
                            <a href="{{ route('help.index') }}#password" class="font-body-sm text-body-sm text-primary hover:underline">Lupa Password?</a>
                        </div>
                        <div class="relative flex items-center bg-surface-container rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-outline text-xl mr-2.5">lock</span>
                            <input type="password" name="password" required class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-mono" placeholder="Masukkan password kamu" data-password-toggle>
                        </div>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded accent-primary">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Ingat perangkat ini selama 30 hari</span>
                    </label>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center justify-center gap-2 hover:bg-primary transition-all shadow-[0_4px_20px_rgba(160,120,255,0.35)]">
                        <span class="material-symbols-outlined text-lg">security</span>
                        <span>Masuk ke Akun GameVault</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>

                    <p class="text-center font-body-sm text-body-sm text-on-surface-variant">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-primary hover:underline font-semibold">Daftar gratis</a>
                    </p>
                </form>

                <div class="flex flex-col gap-4">
                    <div class="p-4 rounded-xl bg-surface-container-lowest flex items-center gap-3 border border-outline-variant/20">
                        <span class="material-symbols-outlined text-secondary text-xl shrink-0">shield</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Setiap login baru dari lokasi yang tidak dikenali akan memerlukan konfirmasi via 2FA (Authenticator App / Recovery Code) dan notifikasi instan.
                        </p>
                    </div>
                    @if(session('two_factor_pending'))
                        <div class="p-4 rounded-xl bg-tertiary-container/20 text-tertiary font-body-sm text-body-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">key</span>
                            Dua langkah verifikasi wajib: masukkan kode 6 digit dari app authenticator kamu.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection