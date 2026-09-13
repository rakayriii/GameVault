@extends('layouts.app')

@section('title', 'Notifikasi - GameVault')

@section('content')
    <div class="relative w-full overflow-hidden">
        <div class="absolute -top-40 right-10 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto px-6 lg:px-8 py-10 relative">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight font-bold">Notifikasi</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                        Aktivitas akun, transaksi, dan pengumuman terbaru.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->unread_notifications_count > 0)
                        <span class="px-2.5 py-1 rounded-full bg-primary-container/20 text-primary font-label-stat text-label-stat font-bold">{{ auth()->user()->unread_notifications_count }} belum dibaca</span>
                    @endif
                    @if($notifications->total())
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-surface-container text-on-surface font-body-sm text-body-sm font-semibold flex items-center gap-2 hover:bg-surface-container-high transition-colors">
                                <span class="material-symbols-outlined text-sm">done_all</span>
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($notifications as $notification)
                    <div class="p-4 rounded-2xl {{ $notification->read_at ? 'bg-surface-container-low' : 'bg-surface-container-low border border-primary/30' }} flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl {{ $notification->read_at ? 'bg-surface-container-high text-on-surface-variant' : 'bg-primary-container/20 text-primary' }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl">{{ match($notification->type) {
                                'order' => 'local_mall',
                                'payment' => 'payments',
                                'wallet' => 'account_balance_wallet',
                                'dispute' => 'gavel',
                                'system' => 'campaign',
                                default => 'notifications',
                            } }}</span>
                        </div>
                        <div class="flex-1 flex flex-col gap-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-headline-sm text-sm font-bold text-on-surface">{{ $notification->title }}</span>
                                @if(! $notification->read_at)
                                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                                @endif
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $notification->body }}</p>
                            <span class="font-label-mono text-label-stat text-outline mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-container-low rounded-2xl p-12 flex flex-col items-center gap-4 text-center">
                        <span class="material-symbols-outlined text-6xl text-outline">notifications_none</span>
                        <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Tidak ada notifikasi</h2>
                        <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                            Semua aktivitas transaksi dan pengumuman penting akan muncul di sini.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="mt-8">{{ $notifications->links() }}</div>
            @endif
        </div>
    </div>
@endsection