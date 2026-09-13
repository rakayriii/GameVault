@extends('layouts.app')

@section('title', 'Listing Akun · Seller Center')

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-1">
                    <a href="{{ route('seller.dashboard') }}" class="hover:text-secondary transition-colors">Seller Center</a>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <span class="text-on-surface font-semibold">Listing Akun</span>
                </nav>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Listing Akun</h1>
            </div>
            <a href="{{ route('seller.accounts.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-lg">add_circle</span>Jual Akun Baru
            </a>
        </div>

        <div class="space-y-4">
            @forelse($accounts as $acc)
                <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 flex flex-wrap items-center gap-4">
                    @if($acc->images->first() ?? false)
                        <img src="{{ $acc->images->first()->url() }}" class="w-20 h-20 rounded-2xl object-cover shrink-0">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0">
                            <x-game-icon :game="$acc->game" size="w-20 h-20" icon="text-3xl" />
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full font-badge text-badge text-[10px] font-semibold
                                {{ $acc->status->value === 'approved' ? 'bg-tertiary-container/30 text-tertiary' : ($acc->status->value === 'pending_review' ? 'bg-secondary-container/30 text-secondary' : ($acc->status->value === 'sold' ? 'bg-primary-container/30 text-primary' : ($acc->status->value === 'rejected' ? 'bg-error-container/30 text-error' : 'bg-surface-container text-on-surface-variant'))) }}">
                                {{ $acc->status->label() }}
                            </span>
                            @if($acc->is_featured) <span class="px-2 py-0.5 rounded-full bg-tertiary-container/30 text-tertiary font-badge text-badge text-[10px]">FEATURED</span> @endif
                        </div>
                        <h3 class="font-body-md text-body-md font-semibold text-on-surface mt-1 line-clamp-1">{{ $acc->title }}</h3>
                        <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $acc->game?->name }} · {{ $acc->server ?: 'Global' }} · {{ $acc->created_at->translatedFormat('d M Y') }}</p>
                        @if($acc->rejection_reason)
                            <p class="font-body-sm text-body-sm text-error mt-1">Ditolak: {{ $acc->rejection_reason }}</p>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <div class="font-label-mono text-label-mono text-tertiary font-semibold">Rp {{ number_format($acc->price, 0, ',', '.') }}</div>
                        <div class="mt-2 flex items-center gap-2 justify-end">
                            @if($acc->status->value === 'approved')
                                <form method="POST" action="{{ route('seller.accounts.toggle', $acc) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="offline">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm hover:text-error hover:bg-error-container/20 transition-colors">Offline</button>
                                </form>
                            @elseif($acc->status->value === 'draft')
                                <form method="POST" action="{{ route('seller.accounts.toggle', $acc) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="online">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-tertiary-container/30 text-tertiary font-body-sm text-body-sm hover:bg-tertiary hover:text-on-primary transition-colors">Live Lagi</button>
                                </form>
                            @endif
                            @if($acc->status->value !== 'sold')
                                <a href="{{ route('seller.accounts.edit', $acc) }}" class="px-3 py-1.5 rounded-xl bg-secondary-container/30 text-secondary font-body-sm text-body-sm hover:bg-secondary hover:text-on-secondary transition-colors">Edit</a>
                            @endif
                            @if(in_array($acc->status->value, ['draft', 'rejected']))
                                <form method="POST" action="{{ route('seller.accounts.destroy', $acc) }}" onsubmit="return confirm('Hapus listing ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm hover:text-error hover:bg-error-container/20 transition-colors">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">inventory_2</span>
                    <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-4">Belum ada listing</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Jual akun pertamamu dan mulai pendapatan.</p>
                    <a href="{{ route('seller.accounts.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                        <span class="material-symbols-outlined text-lg">add_circle</span>Jual Akun Baru
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $accounts->links() }}</div>
    </div>
@endsection