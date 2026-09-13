@extends('layouts.app')

@section('title', 'Seller Requests · Admin')

@section('content')
    <div class="max-w-[1100px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Pengajuan Seller</span>
        </nav>

        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-6">Pengajuan Jadi Seller</h1>

        <div class="space-y-4">
            @forelse($requests as $req)
                <article class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <x-avatar :user="$req->user" class="w-12 h-12 rounded-2xl"/>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-body-md text-body-md font-bold text-on-surface">{{ $req->store_name }}</h3>
                                <span class="px-2.5 py-0.5 rounded-full font-badge text-badge text-[10px] font-semibold
                                    {{ $req->status->value === 'approved' ? 'bg-tertiary-container/30 text-tertiary' : ($req->status->value === 'rejected' ? 'bg-error-container/30 text-error' : 'bg-secondary-container/30 text-secondary') }}">
                                    {{ $req->status->label() }}
                                </span>
                            </div>
                            <p class="font-label-stat text-label-stat text-on-surface-variant">Oleh {{ $req->user->username }} ({{ $req->user->email }}) · {{ $req->created_at->translatedFormat('d M Y H:i') }}</p>
                        </div>
                        @if($req->admin_note)
                            <span class="font-body-sm text-body-sm text-outline">Catatan: {{ $req->admin_note }}</span>
                        @endif
                    </div>

                    <div class="mt-4 grid sm:grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-surface-container p-4">
                            <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Alasan</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $req->reason }}</p>
                        </div>
                        <div class="rounded-2xl bg-surface-container p-4">
                            <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Pengalaman</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $req->experience ?: '—' }}</p>
                        </div>
                    </div>

                    @if($req->status->value === 'pending')
                        <form method="POST" action="{{ route('admin.seller-requests.review', $req) }}" class="mt-4 flex flex-wrap items-end gap-3">
                            @csrf
                            <input name="admin_note" placeholder="Catatan (opsional)" class="flex-1 min-w-[160px] rounded-xl bg-surface-container border border-outline-variant/30 px-3 py-2 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                            <button type="submit" name="action" value="approve" class="px-5 py-2 rounded-xl bg-tertiary-container/40 text-tertiary font-body-sm text-body-sm font-semibold hover:bg-tertiary hover:text-on-primary transition-colors">Setujui & Jadikan Seller</button>
                            <button type="submit" name="action" value="reject" class="px-5 py-2 rounded-xl bg-error-container/30 text-error font-body-sm text-body-sm font-semibold hover:bg-error hover:text-on-error transition-colors">Tolak</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-3xl bg-surface-container-low border border-dashed border-outline-variant/40 p-16 text-center">
                    <span class="material-symbols-outlined text-6xl text-outline">storefront</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-3">Belum ada pengajuan jadi seller.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $requests->links() }}</div>
    </div>
@endsection