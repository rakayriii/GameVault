@extends('layouts.app')

@section('title', 'Kelola Games · Admin Console')

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        @include('partials.flash')

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-secondary transition-colors">Admin Console</a>
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                    <span class="text-on-surface font-semibold">Kelola Games</span>
                </nav>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Kelola Games</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Tambah game, upload logo / ikon, dan atur banner yang tampil di marketplace.</p>
            </div>
            <a href="{{ route('admin.games.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-lg">add_circle</span>Tambah Game
            </a>
        </div>

        <form method="GET" class="mb-6 max-w-md">
            <div class="relative w-full flex items-center bg-surface-container-low rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container transition-all border border-outline-variant/20">
                <span class="material-symbols-outlined text-outline text-xl mr-2.5 select-none">search</span>
                <input name="q" value="{{ request('q') }}" type="text" placeholder="Cari nama game..." class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none">
            </div>
        </form>

        <div class="overflow-hidden rounded-3xl bg-surface-container-low border border-outline-variant/20">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-outline-variant/20 font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">
                        <th class="px-5 py-4">Game</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Listing Live</th>
                        <th class="px-5 py-4">Urutan</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($games as $game)
                        <tr class="border-b border-outline-variant/10 last:border-0 hover:bg-surface-container/60 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($game->iconUrl())
                                        <img src="{{ $game->iconUrl() }}" alt="{{ $game->name }}" class="w-10 h-10 rounded-xl object-cover bg-surface-container-high">
                                    @else
                                        <span class="w-10 h-10 rounded-xl flex items-center justify-center font-headline-sm text-headline-sm font-bold" style="background-color: {{ $game->icon_color }}20; color: {{ $game->icon_color }}">{{ str($game->name)->upper()->substr(0, 2) }}</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $game->name }}</p>
                                        <p class="font-label-stat text-label-stat text-outline">{{ $game->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full font-badge text-badge text-[10px] font-semibold {{ $game->is_active ? 'bg-tertiary-container/30 text-tertiary' : 'bg-surface-container text-on-surface-variant' }}">
                                    {{ $game->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                @if($game->banner_url)
                                    <span class="ml-1.5 px-2 py-1 rounded-full bg-secondary-container/30 text-secondary font-badge text-badge text-[10px]">Banner</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-label-mono text-label-mono text-on-surface">{{ $game->accounts_count }}</td>
                            <td class="px-5 py-3.5 font-label-mono text-label-mono text-on-surface-variant">{{ $game->sort_order }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.games.toggle', $game) }}">
                                    @csrf
                                    <button type="submit" title="{{ $game->is_active ? 'Nonaktifkan game' : 'Aktifkan game' }}" class="p-2 rounded-xl {{ $game->is_active ? 'bg-surface-container text-on-surface-variant hover:text-error hover:bg-error-container/20' : 'bg-secondary-container/30 text-secondary hover:bg-secondary hover:text-on-secondary' }} transition-colors">
                                        <span class="material-symbols-outlined text-lg">{{ $game->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                    </button>
                                </form>
                                <a href="{{ route('admin.games.edit', $game) }}" class="px-3 py-1.5 rounded-xl bg-secondary-container/30 text-secondary font-body-sm text-body-sm hover:bg-secondary hover:text-on-secondary transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.games.destroy', $game) }}" onsubmit="return confirm('Hapus game {{ $game->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm hover:text-error hover:bg-error-container/20 transition-colors">Hapus</button>
                                </form>
                            </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-6xl text-outline">sports_esports</span>
                                <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mt-4">Belum ada game</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Tambahkan game pertama dan upload logonya.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $games->links() }}</div>
    </div>
@endsection