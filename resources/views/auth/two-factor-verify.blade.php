@extends('layouts.app')

@section('title', 'Verifikasi 2FA - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute top-20 right-10 w-96 h-96 bg-tertiary-container/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 left-1/4 w-96 h-96 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-2xl mx-auto px-6 lg:px-8 py-12 md:py-20 relative z-10">
            @include('partials.flash')

            <div class="bg-surface-container-low/95 backdrop-blur-xl rounded-2xl shadow-xl p-6 sm:p-10 flex flex-col gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-tertiary-container/30 text-tertiary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl">shield_with_heart</span>
                    </div>
                    <div>
                        <span class="font-label-stat text-label-stat px-2 py-0.5 rounded-full bg-tertiary-container/20 text-tertiary font-bold tracking-wider block w-fit">VERIFIKASI 2FA DIWAJIBKAN</span>
                        <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold mt-1">Konfirmasi Identitas Kamu</h1>
                    </div>
                </div>

                <p class="font-body-md text-body-md text-on-surface-variant">
                    Login dari perangkat baru terdeteksi. Masukkan kode <span class="text-on-surface font-semibold">6 digit</span> dari Authenticator App kamu untuk melanjutkan. Kode hanya berlaku <span class="text-tertiary font-semibold">60 detik</span>.
                </p>

                <form method="POST" action="{{ route('two-factor.verify') }}" class="flex flex-col gap-5">
                    @csrf
                    <div class="flex items-center justify-center gap-2" data-otp-input>
                        @for($i = 0; $i < 6; $i++)
                            <input type="text" inputmode="numeric" maxlength="1" name="otp_boxes"
                                   class="otp-box w-12 h-14 sm:w-14 sm:h-16 rounded-xl bg-surface-container text-center font-label-mono text-headline-md font-bold text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary border border-outline-variant/30 transition-all"
                                   placeholder="●">
                        @endfor
                        <input type="hidden" name="code" id="otp-combined">
                    </div>

                    @if($errors->any())
                        <div class="p-3 rounded-xl bg-error-container/20 text-error font-body-sm text-body-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">error</span>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center justify-center gap-2 hover:bg-primary transition-all shadow-[0_4px_20px_rgba(160,120,255,0.35)]">
                        <span class="material-symbols-outlined text-lg">verified_user</span>
                        <span>Verifikasi &amp; Masuk</span>
                    </button>
                </form>

                <div class="flex flex-col gap-2 pt-2">
                    <p class="text-center font-body-sm text-body-sm text-on-surface-variant">
                        Tidak punya akses ke app authenticator?
                        <button type="button" data-toggle-recovery class="text-primary hover:underline font-semibold">Gunakan Recovery Code</button>
                    </p>

                    <form method="POST" action="{{ route('two-factor.verify') }}" id="recovery-form" class="hidden flex-col gap-3 bg-surface-container p-4 rounded-xl">
                        @csrf
                        <input type="hidden" name="recovery" value="1">
                        <input type="text" name="recovery_code" class="w-full bg-surface-container-high rounded-xl px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none font-mono" placeholder="Recovery code (8 digit)">
                        <button type="submit" class="w-full py-3 rounded-xl bg-surface-container-high text-on-surface font-headline-sm text-sm font-bold hover:bg-primary hover:text-on-primary transition-all">Verifikasi dengan Recovery Code</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const boxes = document.querySelectorAll('.otp-box');
            const combined = document.getElementById('otp-combined');
            const toggle = document.querySelector('[data-toggle-recovery]');
            const recovery = document.getElementById('recovery-form');

            if (toggle && recovery) {
                toggle.addEventListener('click', () => {
                    recovery.classList.toggle('hidden');
                    recovery.classList.toggle('flex');
                });
            }

            boxes.forEach((box, i) => {
                box.addEventListener('input', () => {
                    box.value = box.value.replace(/[^0-9]/g, '').slice(-1);
                    const value = Array.from(boxes).map(b => b.value).join('');
                    if (combined) combined.value = value;
                    if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
                });
                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !box.value && i > 0) boxes[i - 1].focus();
                });
                box.addEventListener('paste', (e) => {
                    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    text.split('').forEach((ch, j) => { if (boxes[j]) boxes[j].value = ch; });
                    if (combined) combined.value = text;
                    const next = boxes[Math.min(text.length, 5)];
                    if (next) next.focus();
                    e.preventDefault();
                });
            });
        })();
    </script>
@endsection