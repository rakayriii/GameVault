@extends('layouts.app')

@section('title', 'Pusat Bantuan - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-tertiary-container/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-6 lg:px-8 py-10 relative">
            <div class="mb-8">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-secondary-container/20 text-secondary font-label-stat text-label-stat tracking-wider mb-3">
                    <span class="material-symbols-outlined text-sm">support_agent</span>
                    PUSAT BANTUAN 24/7
                </span>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold">
                    Bagaimana Kami <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-tertiary">Bisa Membantu?</span>
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                    Pertanyaan umum seputar rekber, escrow, handover, dispute, dan keamanan akun.
                </p>
            </div>

            <div id="password" class="p-4 rounded-2xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center gap-3 mb-8">
                <span class="material-symbols-outlined text-secondary text-2xl shrink-0">live_help</span>
                <p class="font-body-sm text-body-sm text-on-surface-variant flex-1">
                    Lupa password? Gunakan <span class="text-primary font-semibold">Perbarui Kata Sandi</span> di pengaturan akun, atau hubungi tim kami.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 mb-10">
                @foreach($faqs as $index => $faq)
                    <div class="rounded-2xl bg-surface-container-low overflow-hidden" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                        <button type="button" class="w-full p-5 flex items-center justify-between gap-4 text-left" @click="open = !open">
                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $faq['q'] }}</span>
                            <span class="material-symbols-outlined text-primary transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="open" x-collapse>
                            <p class="px-5 pb-5 font-body-sm text-body-sm text-on-surface-variant leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-6 rounded-2xl bg-gradient-to-r from-primary-container/20 via-surface-container-low to-secondary-container/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-4xl text-primary">headset_mic</span>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Masih butuh bantuan?</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Tim support kami siap membantu 24/7.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('notifications.index') }}" class="px-5 py-3 rounded-xl bg-primary-container text-on-primary-container font-headline-sm text-sm font-bold flex items-center gap-2 hover:bg-primary transition-all">
                        <span class="material-symbols-outlined text-lg">send</span>
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection