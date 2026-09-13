@extends('layouts.app')

@section('title', 'Detail Pesanan · ' . $order->order_no)

@section('content')
    <div class="max-w-[1180px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('orders.index') }}" class="hover:text-secondary transition-colors">Pesanan Saya</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">{{ $order->order_no }}</span>
        </nav>

        <div class="flex flex-wrap items-center gap-4 mb-6">
            <span class="px-3 py-1.5 rounded-full font-badge text-badge font-semibold
                @if($order->status->value === 'completed') bg-tertiary-container/30 text-tertiary
                @elseif(in_array($order->status->value, ['escrow','handover'])) bg-secondary-container/30 text-secondary
                @elseif($order->status->value === 'disputed') bg-error-container/30 text-error
                @else bg-surface-container text-on-surface-variant @endif">
                {{ $order->status->label() }}
            </span>
            <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">{{ $order->order_no }}</h1>
        </div>

        <div class="grid lg:grid-cols-5 gap-8 items-start">
            <div class="lg:col-span-3 space-y-6">
                {{-- Account info --}}
                <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 flex gap-5 items-start">
                    @if($order->gameAccount?->images->first())
                        <img src="{{ asset($order->gameAccount->images->first()->url()) }}" alt="{{ $order->gameAccount->title }}" class="w-28 h-28 rounded-2xl object-cover shrink-0">
                    @else
                        <div class="w-28 h-28 rounded-2xl bg-primary-container/30 flex items-center justify-center text-secondary shrink-0">
                            <x-game-icon :game="$order->gameAccount?->game" size="w-28 h-28" icon="text-5xl" />
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h2 class="font-body-lg text-body-lg font-bold text-on-surface">{{ $order->gameAccount?->title ?? 'Akun sudah dihapus' }}</h2>
                        <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $order->gameAccount?->game?->name }} · {{ $order->gameAccount?->server ?: 'Global' }}</p>
                        @if($order->gameAccount)
                            <a href="{{ route('marketplace.show', $order->gameAccount) }}" class="inline-flex items-center gap-1 mt-2 text-secondary font-body-sm text-body-sm hover:text-tertiary transition-colors">
                                <span class="material-symbols-outlined text-sm">visibility</span>Lihat Detail Akun
                            </a>
                        @endif
                    </div>
                </section>

                {{-- Timeline --}}
                <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6">
                    <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">timeline</span>Timeline Transaksi
                    </h3>
                    <ol class="relative border-l border-outline-variant/30 ml-2">
                        @php
                            $timeline = [
                                ['label' => 'Pesanan Dibuat', 'time' => $order->created_at, 'done' => true, 'color' => 'bg-outline'],
                                ['label' => 'Pembayaran Diterima', 'time' => $order->payments()->first()?->paid_at, 'done' => in_array($order->status->value, ['paid','escrow','handover','completed','disputed']), 'color' => 'bg-secondary'],
                                ['label' => 'Dana di Escrow Vault', 'time' => $order->payments()->first()?->paid_at, 'done' => in_array($order->status->value, ['escrow','handover','completed']), 'color' => 'bg-secondary'],
                                ['label' => 'Handover Kredensial', 'time' => $order->handover_deadline && $order->status->value === 'handover' ? now()->subMinutes(5) : null, 'done' => in_array($order->status->value, ['handover','completed']), 'color' => 'bg-tertiary'],
                                ['label' => 'Dana Dirilis ke Seller', 'time' => $order->completed_at, 'done' => $order->completed_at, 'color' => 'bg-tertiary'],
                            ];
                        @endphp
                        @foreach($timeline as $i => $t)
                            <li class="mb-5 last:mb-0">
                                <span class="absolute -left-[11px] w-6 h-6 rounded-full flex items-center justify-center {{ $t['done'] ? $t['color'] : 'bg-surface-container-high border-2 border-outline-variant/40' }}">
                                    @if($t['done'])
                                        <span class="material-symbols-outlined text-on-primary text-sm">check</span>
                                    @endif
                                </span>
                                <span class="font-body-sm text-body-sm {{ $t['done'] ? 'text-on-surface font-semibold' : 'text-on-surface-variant' }}">{{ $t['label'] }}</span>
                                @if($t['time'])
                                    <span class="block font-label-stat text-label-stat text-outline mt-0.5">{{ $t['time']->translatedFormat('d M Y, H:i') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>

                {{-- Review Form (buyer only, completed, no existing review) --}}
                @if(auth()->id() === $order->buyer_id && $order->status->value === 'completed' && !$order->review)
                    <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-6" x-data="{ rating: 0, hovered: 0 }">
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">Beri Ulasan</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mb-4">Berikan penilaian untuk pengalaman transaksi ini.</p>
                        <form method="POST" action="{{ route('orders.review', $order) }}">
                            @csrf
                            <div class="flex items-center gap-1 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" @mouseenter="hovered = {{ $i }}" @mouseleave="hovered = 0"
                                        class="transition-colors">
                                        <span class="material-symbols-outlined text-4xl"
                                            :class="hovered >= {{ $i }} || rating >= {{ $i }} ? 'text-secondary filled' : 'text-outline'">star</span>
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" :value="rating" required>
                                <span class="font-body-sm text-body-sm text-on-surface-variant ml-3" x-text="rating ? rating + '/5' : '0/5'">0/5</span>
                            </div>
                            <textarea name="content" rows="3" placeholder="Ceritakan pengalaman kamu (opsional)" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 p-4 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none"></textarea>
                            <button type="submit" class="mt-3 inline-flex items-center gap-2 rounded-2xl bg-primary-container text-on-primary-container px-5 py-2.5 font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">
                                <span class="material-symbols-outlined text-lg">rate_review</span>Kirim Ulasan
                            </button>
                        </form>
                    </section>
                @endif

                {{-- Existing review --}}
                @if($order->review)
                    <section class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">Ulasan Transaksi</span>
                            <div class="flex items-center gap-0.5 text-secondary">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-lg {{ $i <= $order->review->rating ? 'filled' : 'text-outline' }}">star</span>
                                @endfor
                            </div>
                        </div>
                        @if($order->review->content)
                            <p class="font-body-md text-body-md text-on-surface-variant mt-1">{{ $order->review->content }}</p>
                        @endif
                    </section>
                @endif
            </div>

            <aside class="lg:col-span-2 lg:sticky top-36 space-y-6">
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Rincian Pembayaran</span>
                    <div class="mt-4 space-y-2.5 font-body-md text-body-md">
                        <div class="flex justify-between text-on-surface-variant"><span>Subtotal</span><span class="font-label-mono text-on-surface">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-tertiary"><span>Diskon</span><span class="font-label-mono">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span></div>
                        @endif
                        <div class="flex justify-between text-on-surface-variant"><span>Biaya Escrow</span><span class="font-label-mono text-on-surface">{{ $order->fee_amount > 0 ? 'Rp ' . number_format($order->fee_amount, 0, ',', '.') : 'Rp 0' }}</span></div>
                        <div class="flex justify-between pt-2 border-t border-outline-variant/30">
                            <span class="font-headline-sm font-bold text-on-surface">Total Dibayar</span>
                            <span class="font-headline-sm font-bold text-tertiary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 space-y-3">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Pihak Transaksi</span>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container">
                        <x-avatar :user="$order->buyer" class="w-10 h-10 rounded-full shrink-0"/>
                        <div class="min-w-0">
                            <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider">Buyer</span>
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $order->buyer->username }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container">
                        <x-avatar :user="$order->seller" class="w-10 h-10 rounded-full shrink-0"/>
                        <div class="min-w-0">
                            <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider">Seller</span>
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $order->seller->username }}</p>
                            @if($order->seller->sellerProfile)
                                <p class="font-label-stat text-label-stat text-secondary">{{ $order->seller->sellerProfile->store_name }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5 space-y-3">
                    @if(in_array($order->status->value, ['escrow','handover']))
                        <a href="{{ route('orders.handover', $order) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-secondary-container/30 text-secondary font-body-sm text-body-sm font-semibold py-3 hover:bg-secondary hover:text-on-secondary transition-colors">
                            <span class="material-symbols-outlined text-lg">open_in_new</span>Buka Ruang Handover
                        </a>
                    @endif
                    @if(auth()->id() === $order->buyer_id && in_array($order->status->value, ['escrow','handover']))
                        <form method="POST" action="{{ route('orders.dispute', $order) }}">
                            @csrf
                            <input type="hidden" name="category" value="other">
                            <input type="hidden" name="description" value="Buyer membuka dispute dari halaman detail pesanan.">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-error/40 text-error font-body-sm text-body-sm font-semibold py-3 hover:bg-error-container/20 transition-colors" onclick="return confirm('Buka dispute? Tim Rekber akan segera menangani.')">
                                <span class="material-symbols-outlined text-lg">gavel</span>Buka Dispute
                            </button>
                        </form>
                    @endif
                    @if($order->gameAccount)
                        <a href="{{ route('marketplace.show', $order->gameAccount) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-outline-variant/40 text-on-surface-variant font-body-sm text-body-sm font-semibold py-3 hover:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-lg">visibility</span>Lihat Akun di Marketplace
                        </a>
                    @endif
                </div>
            </aside>
        </div>
    </div>
@endsection