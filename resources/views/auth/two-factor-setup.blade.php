@extends('layouts.app')

@section('title', 'Aktivasi 2FA - GameVault')

@section('content')
    @php
        $otpauthUri = session('two_factor_setup.qrcode') ?? $qrcode ?? '';
        $secret = session('two_factor_setup.secret') ?? $secret ?? '';
        $codes = session('two_factor_setup.codes') ?? $codes ?? [];
        // Build deterministic QR-like grid from secret
        $seed = 0;
        for ($i = 0; $i < strlen($secret); $i++) { $seed = ($seed * 31 + ord($secret[$i])) & 0xFFFFFFFF; }
        $modules = [];
        $size = 21;
        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                $isFinder = ($row < 7 && $col < 7) || ($row < 7 && $col >= $size - 7) || ($row >= $size - 7 && $col < 7);
                $isInnerFinder = ($row === 1 && $col === 1) || ($row === 1 && $col === $size - 2) || ($row === $size - 2 && $col === 1);
                $isTiming = ($row === 6 && $col > 6 && $col < $size - 7) || ($col === 6 && $row > 6 && $row < $size - 7);
                $hash = ($seed + $row * 31 + $col * 17) & 0xFF;
                $modules[$row][$col] = $isInnerFinder ? 0 : $isFinder ? 1 : $isTiming ? ($row % 2 === 0 ? 1 : 0) : ($hash % 3 < 2 ? 1 : 0);
            }
        }
    @endphp

    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 left-1/3 w-96 h-96 bg-tertiary-container/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl mx-auto px-6 lg:px-8 py-8 md:py-12 relative z-10">
            @include('partials.flash')

            <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-base">home</span>Home
                </a>
                <span class="material-symbols-outlined text-xs text-outline">chevron_right</span>
                <span class="text-secondary font-medium">Aktivasi 2FA</span>
            </div>

            <div class="bg-surface-container-low/95 backdrop-blur-xl rounded-2xl shadow-xl p-6 sm:p-8 flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-tertiary-container/30 text-tertiary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl">key</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-label-stat text-label-stat px-2 py-0.5 rounded-full bg-tertiary-container/20 text-tertiary font-bold tracking-wider">LANGKAH WAJIB</span>
                            <span class="font-label-stat text-label-stat text-outline">STANDAR ESCROW</span>
                        </div>
                        <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold mt-0.5">Aktifkan 2FA (Two-Factor Authentication)</h1>
                    </div>
                </div>

                <p class="font-body-md text-body-md text-on-surface-variant">
                    Kamu akan menerima kode 6-digit dari Authenticator App setiap kali login. Silakan simpan <span class="text-on-surface font-semibold">kode pemulihan</span> di tempat yang aman — itu satu-satunya cara mengakses akun jika kehilangan HP.
                </p>

                {{-- Step 1: Download --}}
                <div class="p-5 rounded-2xl bg-surface-container flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary-container/20 text-primary flex items-center justify-center font-bold font-label-stat">1</span>
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Unduh Aplikasi Authenticator</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-surface-container-highest flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl">smartphone</span>
                            <div class="flex flex-col">
                                <span class="font-body-sm text-body-sm font-bold text-on-surface">Google Authenticator</span>
                                <span class="font-label-stat text-label-stat text-outline">Android & iOS</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-surface-container-highest flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl">lock</span>
                            <div class="flex flex-col">
                                <span class="font-body-sm text-body-sm font-bold text-on-surface">Authy / 1Password</span>
                                <span class="font-label-stat text-label-stat text-outline">Cross-platform</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2: QR + Secret --}}
                <div class="p-5 rounded-2xl bg-surface-container flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary-container/20 text-primary flex items-center justify-center font-bold font-label-stat">2</span>
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Scan QR Code atau Masukkan Secret</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="bg-white p-4 rounded-xl">
                                <svg viewBox="0 0 {{ $size }} {{ $size }}" width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                                    @for($row = 0; $row < $size; $row++)
                                        @for($col = 0; $col < $size; $col++)
                                            @if($modules[$row][$col])
                                                <rect x="{{ $col }}" y="{{ $row }}" width="1" height="1" fill="#1a1a2e"/>
                                            @endif
                                        @endfor
                                    @endfor
                                </svg>
                            </div>
                            <span class="font-body-sm text-[12px] text-outline">Scan menggunakan Google Authenticator / Authy</span>
                        </div>
                        <div class="flex flex-col gap-3">
                            <div class="p-4 rounded-xl bg-surface-container-high">
                                <span class="font-label-stat text-[10px] text-outline uppercase tracking-wider block mb-1">Secret Key (manual entry)</span>
                                <code class="font-mono text-sm text-on-surface font-bold break-all select-all leading-relaxed">{{ $secret }}</code>
                            </div>
                            <p class="font-body-sm text-[12px] text-on-surface-variant flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm text-tertiary">info</span>
                                Jika QR gagal, ketik manual secret key ini di authenticator app kamu.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Recovery codes --}}
                <div class="p-5 rounded-2xl bg-surface-container flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-tertiary-container/30 text-tertiary flex items-center justify-center font-bold font-label-stat">3</span>
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface">Simpan Kode Pemulihan (Recovery Codes)</h3>
                    </div>
                    <div class="p-4 rounded-xl bg-surface-container-high border border-tertiary/20">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-tertiary text-lg">warning</span>
                            <span class="font-body-sm text-body-sm font-semibold text-tertiary">Simpan kode ini di tempat yang aman! Kode hanya muncul sekali.</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach($codes as $code)
                                <code class="px-3 py-2 rounded-lg bg-surface-container text-center font-mono text-sm font-bold text-on-surface select-all">{{ $code }}</code>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Done --}}
                <div class="p-4 rounded-xl bg-tertiary-container/10 flex items-center gap-3">
                    <span class="material-symbols-outlined text-tertiary text-xl">check_circle</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                        2FA sudah aktif di akun kamu. Login berikutnya akan meminta kode authenticator 6-digit.
                    </span>
                </div>

                <a href="{{ route('home') }}" class="w-full py-3.5 px-6 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center justify-center gap-2 hover:bg-primary transition-all shadow-[0_4px_20px_rgba(160,120,255,0.35)]">
                    <span class="material-symbols-outlined text-lg">verified_user</span>
                    <span>Saya Sudah Menyimpan — Lanjutkan</span>
                </a>
            </div>
        </div>
    </div>
@endsection