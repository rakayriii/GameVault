@extends('layouts.app')

@section('title', 'Review Listing · Admin')

@section('content')
    <div class="max-w-[1220px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Review Listing</span>
        </nav>

        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-6">Review Listing</h1>

        <div class="flex gap-2 overflow-x-auto pb-2 mb-6">
            @php $tabs = ['pending' => 'Menunggu', 'approved' => 'Live', 'rejected' => 'Ditolak', 'sold' => 'Terjual', 'all' => 'Semua']; @endphp
            @foreach($tabs as $val => $label)
                <a href="{{ route('admin.accounts', ['tab' => $val]) }}"
                   class="shrink-0 px-4 py-2 rounded-full font-body-sm text-body-sm font-semibold transition-colors {{ $tab === $val ? 'bg-primary-container text-on-primary-container' : 'bg-surface-container text-on-surface-variant hover:text-on-surface' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse($accounts as $acc)
                <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0 overflow-hidden">
                            @if($acc->game?->iconUrl())
                                <img src="{{ $acc->game->iconUrl() }}" alt="{{ $acc->game->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-3xl">sports_esports</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-body-md text-body-md font-semibold text-on-surface">{{ $acc->title }}</h3>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $acc->game?->name }} · {{ $acc->server ?: 'Global' }} · Price Rp {{ number_format($acc->price, 0, ',', '.') }}</p>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">Seller: <strong class="text-on-surface">{{ $acc->seller?->username }}</strong> ({{ $acc->seller?->seller_profile?->store_name }}) · {{ $acc->created_at->translatedFormat('d M Y H:i') }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full font-badge text-badge text-[10px] font-semibold
                            {{ $acc->status->value === 'approved' ? 'bg-tertiary-container/30 text-tertiary' : ($acc->status->value === 'pending_review' ? 'bg-secondary-container/30 text-secondary' : ($acc->status->value === 'sold' ? 'bg-primary-container/30 text-primary' : 'bg-error-container/30 text-error')) }}">
                            {{ $acc->status->label() }}
                        </span>
                    </div>

                    @if($acc->description)
                        <p class="mt-3 rounded-2xl bg-surface-container p-4 font-body-sm text-body-sm text-on-surface-variant line-clamp-3">{{ $acc->description }}</p>
                    @endif

                    @if($acc->status->value === 'pending_review')
                        <div class="mt-4 flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('admin.accounts.approve', $acc) }}" class="flex gap-2">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-tertiary-container/40 text-tertiary font-body-sm text-body-sm font-semibold hover:bg-tertiary hover:text-on-primary transition-colors">
                                    <span class="material-symbols-outlined text-base align-text-bottom">check_circle</span> Setujui & Live
                                </button>
                                <button type="submit" name="next" value="1" class="px-4 py-2 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm hover:bg-surface-container-high transition-colors">Approve → Berikutnya</button>
                            </form>
                            <form method="POST" action="{{ route('admin.accounts.reject', $acc) }}" class="flex-1 flex gap-2 items-start">
                                @csrf
                                <input name="reason" required placeholder="Alasan penolakan" class="flex-1 rounded-xl bg-surface-container border border-outline-variant/30 px-3 py-2 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-error">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-error-container/30 text-error font-body-sm text-body-sm font-semibold hover:bg-error hover:text-on-error transition-colors">Tolak</button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">inventory_2</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-3">Tidak ada listing pada tab ini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $accounts->links() }}</div>
    </div>
@endsection