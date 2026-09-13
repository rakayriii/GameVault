@props(['game' => null])

@extends('layouts.app')

@section('title', ($game ? 'Edit Game · ' : 'Tambah Game · ').'Admin Console')

@section('content')
    <div class="max-w-[760px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <a href="{{ route('admin.games') }}" class="hover:text-secondary transition-colors">Kelola Games</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">{{ $game ? 'Edit Game' : 'Tambah Game' }}</span>
        </nav>

        <div class="mb-8">
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">{{ $game ? 'Edit Game' : 'Tambah Game Baru' }}</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Logo / ikon dan banner akan tampil di marketplace &amp; seller center.</p>
        </div>

        <form method="POST" enctype="multipart/form-data" action="{{ $game ? route('admin.games.update', $game) : route('admin.games.store') }}" class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-7 space-y-6">
            @csrf
            @if($game)
                @method('PUT')
            @endif

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Nama Game</label>
                    <input name="name" value="{{ old('name', $game?->name) }}" required maxlength="60" placeholder="Ex: Mobile Legends" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
                    @error('name') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Warna Ikon</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="icon_color" value="{{ old('icon_color', $game?->icon_color ?? '#d0bcff') }}" class="h-12 w-14 rounded-xl bg-surface-container border border-outline-variant/30 cursor-pointer">
                        <input name="icon_color" value="{{ old('icon_color', $game?->icon_color ?? '#d0bcff') }}" maxlength="20" class="flex-1 rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Deskripsi Singkat</label>
                <textarea name="description" rows="2" maxlength="500" placeholder="Genre, platform, dsb." class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none">{{ old('description', $game?->description) }}</textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $game?->sort_order ?? 0) }}" min="0" max="1000" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface focus:outline-none focus:border-primary">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 rounded-2xl bg-surface-container p-4 cursor-pointer w-full">
                        <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded accent-secondary" @checked((bool) old('is_active', $game?->is_active ?? true))>
                        <span class="font-body-sm text-body-sm text-on-surface"><strong>Game aktif</strong> <span class="text-on-surface-variant">— tampil di marketplace &amp; form jual.</span></span>
                    </label>
                </div>
            </div>

            <div class="rounded-2xl bg-surface-container border border-outline-variant/30 p-5 space-y-4">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Logo / Ikon Game</label>
                    <div class="flex items-center gap-4">
                        @if($game?->iconUrl())
                            <img src="{{ $game->iconUrl() }}" alt="Logo {{ $game->name }}" class="w-16 h-16 rounded-2xl object-cover bg-surface-container-high border border-outline-variant/20">
                        @endif
                        <label class="flex-1 flex items-center justify-center gap-2 py-4 rounded-xl border-2 border-dashed border-outline-variant/40 hover:border-secondary/60 cursor-pointer transition-colors">
                            <input type="file" name="icon" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="hidden">
                            <span class="material-symbols-outlined text-secondary">image</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant font-semibold">{{ $game?->icon_url ? 'Ganti logo' : 'Pilih logo game' }}</span>
                        </label>
                    </div>
                    <p class="font-label-stat text-label-stat text-on-surface-variant mt-1.5">JPG, PNG, WebP, atau SVG. Maks 2MB.</p>
                    @if($game?->icon_url)
                        <label class="flex items-center gap-2 mt-2 font-body-sm text-body-sm text-error cursor-pointer select-none">
                            <input type="checkbox" name="remove_icon" value="1" class="h-4 w-4 rounded accent-error">
                            Kosongkan logo saat menyimpan
                        </label>
                    @endif
                    @error('icon') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Banner Game <span class="text-outline">(opsional)</span></label>
                    <div class="flex items-center gap-4">
                        @if($game?->bannerUrl())
                            <img src="{{ $game->bannerUrl() }}" alt="Banner {{ $game->name }}" class="h-16 w-28 rounded-xl object-cover bg-surface-container-high border border-outline-variant/20">
                        @endif
                        <label class="flex-1 flex items-center justify-center gap-2 py-4 rounded-xl border-2 border-dashed border-outline-variant/40 hover:border-secondary/60 cursor-pointer transition-colors">
                            <input type="file" name="banner" accept="image/jpeg,image/png,image/webp" class="hidden">
                            <span class="material-symbols-outlined text-secondary">panorama_wide_angle</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant font-semibold">{{ $game?->banner_url ? 'Ganti banner' : 'Pilih banner' }}</span>
                        </label>
                    </div>
                    <p class="font-label-stat text-label-stat text-on-surface-variant mt-1.5">JPG, PNG, atau WebP. Maks 4MB.</p>
                    @if($game?->banner_url)
                        <label class="flex items-center gap-2 mt-2 font-body-sm text-body-sm text-error cursor-pointer select-none">
                            <input type="checkbox" name="remove_banner" value="1" class="h-4 w-4 rounded accent-error">
                            Kosongkan banner saat menyimpan
                        </label>
                    @endif
                    @error('banner') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold px-8 py-3 hover:bg-primary transition-colors">Simpan</button>
                <a href="{{ route('admin.games') }}" class="rounded-2xl bg-surface-container text-on-surface-variant font-body-md text-body-md font-semibold px-8 py-3 hover:bg-surface-container-high transition-colors">Batal</a>
            </div>
        </form>
    </div>
@endsection