@extends('layouts.app')

@section('title', 'Dispute Management · Admin')

@section('content')
    <div class="max-w-[1220px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Dispute</span>
        </nav>

        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-6">Management Dispute</h1>

        <div class="space-y-4">
            @forelse($disputes as $dispute)
                <a href="{{ route('disputes.show', $dispute) }}" class="block group">
                    <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 group-hover:border-error/40 transition-colors">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-label-mono text-label-mono text-on-surface">{{ $dispute->case_no }}</span>
                            <span class="px-2.5 py-0.5 rounded-full font-badge text-badge text-[10px] font-semibold
                                {{ $dispute->status->value === 'resolved' ? 'bg-tertiary-container/30 text-tertiary' : ($dispute->status->value === 'rejected' ? 'bg-surface-container text-on-surface-variant' : 'bg-error-container/30 text-error') }}">
                                {{ $dispute->status->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge text-[10px]">{{ $dispute->priority->label() }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge text-[10px]">{{ $dispute->category_label }}</span>
                        </div>
                        <p class="mt-2 font-body-md text-body-md font-semibold text-on-surface">Pesanan {{ $dispute->order?->order_no }} · {{ $dispute->order?->gameAccount?->title }}</p>
                        <p class="mt-1 font-label-stat text-label-stat text-on-surface-variant">
                            {{ $dispute->openedBy?->username }} <span class="text-outline">vs</span> {{ $dispute->opponent?->username }}
                            · Rp {{ number_format($dispute->order?->total_amount ?? 0, 0, ',', '.') }} · {{ $dispute->created_at->translatedFormat('d M Y H:i') }}
                        </p>
                    </article>
                </a>
            @empty
                <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">gavel</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-3">Tidak ada dispute.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $disputes->links() }}</div>
    </div>
@endsection