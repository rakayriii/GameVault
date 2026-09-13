@extends('layouts.app')

@section('title', 'Riwayat Penarikan · GameVault')

@section('content')
    <div class="max-w-[980px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            @if($isSellerRoute ?? false)
                <a href="{{ route('seller.dashboard') }}" class="hover:text-secondary transition-colors">Seller Center</a>
            @else
                <a href="{{ route('wallet.index') }}" class="hover:text-secondary transition-colors">Wallet</a>
            @endif
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Penarikan Dana</span>
        </nav>

        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-6">Riwayat Penarikan</h1>

        <div class="space-y-4">
            @forelse($withdrawals as $w)
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 flex flex-wrap items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl flex items-center justify-center
                        {{ $w->status->value === 'completed' ? 'bg-tertiary-container/25 text-tertiary' : ($w->status->value === 'rejected' ? 'bg-error-container/25 text-error' : 'bg-secondary-container/25 text-secondary') }}">
                        <span class="material-symbols-outlined">{{ $w->status->value === 'completed' ? 'task_alt' : ($w->status->value === 'rejected' ? 'cancel' : 'schedule') }}</span>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-label-mono text-label-mono text-on-surface">{{ $w->reference }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                            {{ $w->payoutAccount?->bank_name }} •••• {{ substr($w->payoutAccount?->account_number ?? '', -4) }} · {{ $w->created_at->translatedFormat('d M Y, H:i') }}
                        </p>
                        @if($w->admin_note)
                            <p class="font-body-sm text-body-sm text-outline mt-1">Catatan: {{ $w->admin_note }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="font-headline-sm text-headline-sm font-bold text-on-surface">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full font-badge text-badge font-semibold
                            {{ $w->status->value === 'completed' ? 'bg-tertiary-container/30 text-tertiary' : ($w->status->value === 'rejected' ? 'bg-error-container/30 text-error' : 'bg-secondary-container/30 text-secondary') }}">
                            {{ $w->status->label() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">outbox</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-3">Belum ada penarikan dana.</p>
                    <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 mt-4 text-secondary font-body-sm text-body-sm font-semibold">Mulai tarik dana</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $withdrawals->links() }}</div>
    </div>
@endsection