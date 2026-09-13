@extends('layouts.app')

@section('title', 'Dispute & Bantuan · GameVault')

@section('content')
    <div class="max-w-[1000px] mx-auto px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface flex items-center gap-3">
                Dispute & Bantuan
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-error-container/30 text-error font-badge text-badge font-semibold">
                    <span class="material-symbols-outlined text-sm">gavel</span>TIM REKBER
                </span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Semua dispute dengan transaksi escrow ditangani tim rekber GameVault 24/7.</p>
        </div>

        @if(!$disputes->count())
            <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center mb-8">
                <span class="material-symbols-outlined text-6xl text-tertiary">verified_user</span>
                <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-4">Tidak ada dispute aktif</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Semua transaksimu berjalan lancar. Shield escrow tetap mengawasi.</p>
            </div>
        @endif

        <div class="space-y-4">
            @foreach($disputes as $dispute)
                <a href="{{ route('disputes.show', $dispute) }}" class="block">
                    <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 hover:border-error/40 transition-colors">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-label-mono text-label-mono text-outline">{{ $dispute->case_no }}</span>
                            <span class="px-2.5 py-0.5 rounded-full font-badge text-badge font-semibold
                                {{ $dispute->status->value === 'resolved' ? 'bg-tertiary-container/30 text-tertiary' : ($dispute->status->value === 'rejected' ? 'bg-surface-container text-on-surface-variant' : 'bg-error-container/30 text-error') }}">
                                {{ $dispute->status->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge font-semibold">{{ $dispute->category_label }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-body-md text-body-md font-semibold text-on-surface line-clamp-1">Pesanan {{ $dispute->order?->order_no }} · {{ $dispute->order?->gameAccount?->title }}</p>
                                <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $dispute->opened_by === auth()->id() ? 'Dibuka oleh kamu' : 'Dibuka oleh ' . $dispute->openedBy?->username }} · {{ $dispute->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                            <span class="flex items-center gap-1 text-secondary font-body-sm text-body-sm font-semibold">Buka kasus <span class="material-symbols-outlined text-sm">arrow_forward</span></span>
                        </div>
                    </article>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $disputes->links() }}</div>
    </div>
@endsection