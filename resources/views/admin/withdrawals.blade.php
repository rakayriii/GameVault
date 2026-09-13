@extends('layouts.app')

@section('title', 'Withdrawal Processes · Admin')

@section('content')
    <div class="max-w-[1100px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Proses Penarikan</span>
        </nav>

        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-6">Proses Penarikan Dana</h1>

        <div class="space-y-4">
            @forelse($withdrawals as $w)
                <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl flex items-center justify-center
                            {{ $w->status->value === 'completed' ? 'bg-tertiary-container/25 text-tertiary' : ($w->status->value === 'rejected' ? 'bg-error-container/25 text-error' : 'bg-secondary-container/25 text-secondary') }}">
                            <span class="material-symbols-outlined">{{ $w->status->value === 'completed' ? 'task_alt' : ($w->status->value === 'rejected' ? 'cancel' : 'schedule') }}</span>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-label-mono text-label-mono text-on-surface">{{ $w->reference }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge text-[10px]">{{ $w->status->label() }}</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                                {{ $w->user?->username }} ({{ $w->user?->email }}) · {{ $w->created_at->translatedFormat('d M Y H:i') }}
                            </p>
                            <p class="font-label-mono text-label-mono text-tertiary">
                                {{ $w->payoutAccount?->bank_name }} {{ $w->payoutAccount?->account_number }} · {{ $w->payoutAccount?->account_name }}
                            </p>
                            @if($w->admin_note)
                                <p class="font-body-sm text-body-sm text-outline mt-1">Catatan: {{ $w->admin_note }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-headline-sm text-headline-sm font-bold text-on-surface">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                            <div class="font-label-stat text-label-stat text-outline">Fee: Rp {{ number_format($w->fee, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    @if($w->status->value === 'pending')
                        <form method="POST" action="{{ route('admin.withdrawals.process', $w) }}" class="mt-4 flex flex-wrap items-end gap-3">
                            @csrf
                            <input name="admin_note" placeholder="Catatan (opsional)" class="flex-1 min-w-[160px] rounded-xl bg-surface-container border border-outline-variant/30 px-3 py-2 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                            <button type="submit" name="action" value="approve" class="px-5 py-2 rounded-xl bg-tertiary-container/40 text-tertiary font-body-sm text-body-sm font-semibold hover:bg-tertiary hover:text-on-primary transition-colors">Tandai Terkirim</button>
                            <button type="submit" name="action" value="reject" class="px-5 py-2 rounded-xl bg-error-container/30 text-error font-body-sm text-body-sm font-semibold hover:bg-error hover:text-on-error transition-colors">Tolak & Kembalikan</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">outbox</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-3">Tidak ada permintaan penarikan.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $withdrawals->links() }}</div>
    </div>
@endsection