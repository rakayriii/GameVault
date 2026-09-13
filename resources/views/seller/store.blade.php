@extends('layouts.app')

@section('title', 'Profil Toko · Seller Center')

@section('content')
    <div class="max-w-[760px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-secondary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">Profil Toko</span>
        </nav>

        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface mb-6">Profil Toko</h1>

        <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6 mb-6 flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-primary-container/30 flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined text-3xl">storefront</span>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="font-body-lg text-body-lg font-bold text-on-surface">{{ $profile?->store_name }}</h2>
                <p class="font-label-mono text-label-mono text-outline">gamevault.test/store/{{ $profile?->slug }}</p>
            </div>
            <a href="{{ route('store.show', $profile) }}" class="text-secondary font-body-sm text-body-sm font-semibold shrink-0">Lihat Toko</a>
        </div>

        <form method="POST" action="{{ route('seller.store.update') }}" class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-7 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Nama Toko</label>
                <input name="store_name" value="{{ old('store_name', $profile?->store_name) }}" required maxlength="60" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
                @error('store_name') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Bio Toko</label>
                <textarea name="bio" rows="3" maxlength="500" placeholder="Cerita singkat toko & layanan" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none">{{ old('bio', $profile?->bio) }}</textarea>
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Pengumuman Toko</label>
                <input name="announcement" value="{{ old('announcement', $profile?->announcement) }}" maxlength="255" placeholder="Ex: Tersedia garansi 100%, proses kilat 5 menit" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
            </div>

            <button type="submit" class="w-full rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold py-3.5 hover:bg-primary transition-colors">Simpan Profil Toko</button>
        </form>
    </div>
@endsection