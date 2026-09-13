@extends('layouts.app')

@section('title', 'Ruang Handover · ' . $order->order_no)

@section('styles')
    <style>
        .chat-scroll::-webkit-scrollbar { width: 6px; }
        .chat-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 999px; }
        .material-symbols-outlined.filled { font-variation-settings: 'FILL' 1; }
    </style>
@endsection

@section('content')
    @php $isBuyer = auth()->id() === $order->buyer_id; $isSeller = auth()->id() === $order->seller_id; @endphp

    <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-3 justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.show', $order) }}" class="p-2 rounded-xl hover:bg-surface-container-high text-on-surface-variant transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $order->order_no }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full font-badge text-badge font-semibold {{ $order->status->value === 'escrow' ? 'bg-secondary-container/30 text-secondary' : 'bg-tertiary-container/30 text-tertiary' }}">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                    <p class="font-label-stat text-label-stat text-on-surface-variant">{{ $order->gameAccount?->title }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 font-label-mono text-label-mono">
                @if($order->status->value === 'escrow')
                    <span class="px-3 py-1.5 rounded-xl bg-surface-container-low border border-outline-variant/20">Batas escrow: <span class="text-tertiary" data-countdown="{{ $order->escrow_deadline?->timestamp }}"></span></span>
                @endif
                @if($order->status->value === 'handover')
                    <span class="px-3 py-1.5 rounded-xl bg-surface-container-low border border-outline-variant/20">Batas handover: <span class="text-tertiary" data-countdown="{{ $order->handover_deadline?->timestamp }}"></span></span>
                @endif
            </div>
        </div>

        @php $messages = $messages->sortBy('created_at')->values(); @endphp

        <div class="grid lg:grid-cols-3 gap-6 items-start">
            {{-- Chat / credential stream --}}
            <div class="lg:col-span-2 rounded-3xl bg-surface-container-low border border-outline-variant/20 flex flex-col overflow-hidden">
                <div class="px-5 py-3.5 border-b border-outline-variant/20 flex items-center justify-between">
                    <span class="font-body-sm text-body-sm font-semibold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-lg">chat</span>Ruang Aman Transaksi
                    </span>
                    <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider">Hanya buyer, seller & tim rekber</span>
                </div>

                <div class="flex-1 min-h-[480px] max-h-[560px] overflow-y-auto p-5 space-y-4 chat-scroll" id="chatBox">
                    @forelse($messages as $message)
                        @php $mine = $message->sender_id === auth()->id(); @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[82%]">
                                <div class="flex items-center gap-2 mb-1 {{ $mine ? 'justify-end' : '' }}">
                                    @if(!$mine)
                                        <x-avatar :user="$message->sender" class="w-6 h-6 rounded-full"/>
                                    @endif
                                    <span class="font-label-stat text-label-stat text-on-surface-variant">{{ $mine ? 'Kamu' : $message->sender->username }}</span>
                                    <span class="font-label-stat text-[10px] text-outline">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                                <div class="rounded-2xl px-4 py-3 font-body-sm text-body-sm leading-relaxed
                                    {{ $message->type->value === 'credential'
                                        ? 'bg-tertiary-container/25 border border-tertiary/40 text-on-surface'
                                        : ($mine ? 'bg-primary-container/40 text-on-surface' : 'bg-surface-container-high text-on-surface') }}">
                                    @if($message->type->value === 'credential')
                                        <div class="flex items-center gap-1.5 mb-2 text-tertiary font-label-stat text-label-stat font-semibold uppercase tracking-wide">
                                            <span class="material-symbols-outlined text-sm">lock</span>Kredensial Akun Dikirim
                                        </div>
                                    @endif
                                    <div class="{{ $message->type->value === 'credential' ? 'font-label-mono text-label-mono whitespace-pre-wrap' : '' }}">{{ $message->body }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center py-16">
                            <span class="material-symbols-outlined text-5xl text-outline">forum</span>
                            <p class="font-body-md text-body-md text-on-surface-variant mt-3 w-64">
                                @if($status_tip = ($isSeller ? 'Tunggu pembayaran buyer, lalu pilih "Kirim Kredensial".' : 'Menunggu seller mengirim kredensial akun.'))
                                    {{ $status_tip }}
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Composer --}}
                @if($isBuyer || $isSeller || auth()->user()->isStaff())
                    <div class="border-t border-outline-variant/20 p-4 bg-surface-container/60" x-data="{ body: '', asCredential: {{ $isSeller ? 'true' : 'false' }}, viaStaff: false }">
                        @if($isSeller && in_array($order->status->value, ['escrow', 'handover']))
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Mode Kirim</span>
                                    @if(!auth()->user()->isStaff())
                                        <label class="flex items-center gap-2 text-secondary font-body-sm text-body-sm cursor-pointer">
                                            <input type="checkbox" x-model="asCredential" class="accent-secondary">
                                            Kirim sebagai kredensial rahasia
                                        </label>
                                    @endif
                                </div>
                            </div>
                        @elseif(auth()->user()->isStaff())
                            <p class="mb-2 font-label-stat text-label-stat text-secondary uppercase tracking-wider">Mode tim rekber (staff)</p>
                        @endif

                        <form method="POST" action="{{ route('orders.message', $order) }}">
                            @csrf
                            <div class="flex items-end gap-3">
                                <textarea name="body" x-model="body" rows="1" required placeholder="{{ $isSeller ? 'Tempel login & password akun di sini, atau kirim catatan…' : 'Tulis pesan…' }}" class="flex-1 rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none" x-on:input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"></textarea>
                                @if($isSeller)
                                    <input type="hidden" name="credential" :value="asCredential ? '1' : '0'">
                                @endif
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-5 py-3 rounded-2xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors shrink-0" :disabled="!body.trim()">
                                    <span class="material-symbols-outlined text-lg">send</span>Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Right column --}}
            <aside class="space-y-6">
                {{-- Progress --}}
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Status Transaksi</span>
                    <div class="mt-4 space-y-3">
                        @php
                            $steps = [
                                ['label' => 'Pembayaran diterima, dana di escrow', 'done' => in_array($order->status->value, ['escrow','handover','completed']), 'icon' => 'lock'],
                                ['label' => 'Seller mengirim kredensial akun', 'done' => in_array($order->status->value, ['handover','completed']), 'icon' => 'key'],
                                ['label' => 'Buyer verifikasi akun & ganti sandi', 'done' => $order->status->value === 'completed', 'icon' => 'verified_user'],
                                ['label' => 'Dana dirilis ke seller', 'done' => $order->status->value === 'completed', 'icon' => 'payments'],
                            ];
                        @endphp
                        @foreach($steps as $s)
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl flex items-center justify-center {{ $s['done'] ? 'bg-tertiary-container/40 text-tertiary' : 'bg-surface-container-high text-outline' }}">
                                    <span class="material-symbols-outlined text-lg">{{ $s['done'] ? 'check' : $s['icon'] }}</span>
                                </span>
                                <span class="font-body-sm text-body-sm {{ $s['done'] ? 'text-on-surface' : 'text-on-surface-variant' }}">{{ $s['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Dev info for seller/buyer --}}
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Akun yang Dijual</span>
                    <div class="mt-3 space-y-2 font-body-sm text-body-sm text-on-surface-variant">
                        @if($order->gameAccount)
                            <p class="text-on-surface font-semibold line-clamp-2">{{ $order->gameAccount->title }}</p>
                            <p>{{ $order->gameAccount->game?->name }} · {{ $order->gameAccount->server ?: 'Global' }}</p>
                            @if($order->gameAccount->rank) <p>Rank: {{ $order->gameAccount->rank }}</p> @endif
                            @if($order->gameAccount->handover_note)
                                <div class="rounded-xl bg-tertiary-container/15 border border-tertiary/30 p-3 text-on-surface-variant">💡 {{ $order->gameAccount->handover_note }}</div>
                            @endif
                        @else
                            <p class="text-on-surface-variant">Detail akun tidak tersedia.</p>
                        @endif
                    </div>
                </div>

                {{-- Buyer confirm --}}
                @if($isBuyer && in_array($order->status->value, ['escrow','handover']))
                    <div class="rounded-3xl bg-tertiary-container/15 border border-tertiary/40 p-5" x-data="{ confirmCheck: false }">
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-tertiary">check_circle</span>
                            @if($order->status->value === 'escrow')
                                Menunggu Kredensial
                            @else
                                Verifikasi Akun Selesai?
                            @endif
                        </h3>
                        @if($order->status->value === 'handover')
                            <ol class="mt-3 space-y-1.5 font-body-sm text-body-sm text-on-surface-variant list-decimal list-inside marker:text-tertiary">
                                <li>Login ke akun dengan kredensial yang dikirim seller.</li>
                                <li>Ganti <strong class="text-on-surface">email & password</strong> akun ke milikmu.</li>
                                <li>Pastikan semua yang dijanjikan sesuai (skin, rank, hero).</li>
                            </ol>
                            <form method="POST" action="{{ route('checkout.confirm', $order) }}" class="mt-4">
                                @csrf
                                <label class="flex items-start gap-2.5 cursor-pointer mb-3">
                                    <input type="checkbox" name="confirm_check" value="1" class="mt-0.5 h-5 w-5 rounded accent-tertiary" x-model="confirmCheck">
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Saya sudah memverifikasi akun & mengganti email/password.</span>
                                </label>
                                <button type="submit" :disabled="!confirmCheck" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-tertiary text-on-primary font-body-sm text-body-sm font-bold py-3 hover:bg-on-tertiary transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined text-lg">how_to_reg</span>Konfirmasi & Rilis Dana ke Seller
                                </button>
                            </form>
                            <p class="mt-3 text-center font-label-stat text-label-stat text-outline">Setelah konfirmasi, dana escrow dirilis otomatis.</p>
                        @else
                            <p class="font-body-md text-body-md text-on-surface-variant mt-2">Kredensial akan muncul di ruang ini. Kamu akan menerima notifikasi begitu seller mengirim.</p>
                        @endif
                    </div>
                @endif

                {{-- Staff quick panel --}}
                @if(auth()->user()->isStaff())
                    <div class="rounded-3xl bg-primary-container/15 border border-primary/40 p-5">
                        <h3 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">admin_panel_settings</span>Panel Tim Rekber
                        </h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">Status: <strong class="text-on-surface">{{ $order->status->label() }}</strong></p>
                        <a href="{{ route('admin.disputes') }}" class="inline-flex items-center gap-2 mt-3 text-primary font-body-sm text-body-sm font-semibold">Kelola dispute <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                    </div>
                @endif

                {{-- Dispute --}}
                @if(($isBuyer || $isSeller) && in_array($order->status->value, ['escrow','handover']))
                    <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-4">
                        <p class="font-body-sm text-body-sm text-on-surface-variant mb-3">Cara lain? Buka dispute untuk dibantu tim rekber.</p>
                        <a href="{{ route('disputes.index') }}" class="inline-flex items-center justify-center gap-2 w-full rounded-2xl border border-error/40 text-error font-body-sm text-body-sm font-semibold py-2.5 hover:bg-error-container/15 transition-colors">
                            <span class="material-symbols-outlined text-lg">gavel</span>Hubungi Tim Rekber
                        </a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const chatBox = document.getElementById('chatBox');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endpush