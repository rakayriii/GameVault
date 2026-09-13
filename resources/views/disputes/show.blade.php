@extends('layouts.app')

@section('title', 'Kasus ' . $dispute->case_no)

@section('content')
    <div class="max-w-[1100px] mx-auto px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant mb-6">
            <a href="{{ route('disputes.index') }}" class="hover:text-secondary transition-colors">Dispute</a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-on-surface font-semibold">{{ $dispute->case_no }}</span>
        </nav>

        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-error-container/30 flex items-center justify-center text-error">
                    <span class="material-symbols-outlined">gavel</span>
                </span>
                <div>
                    <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">{{ $dispute->case_no }}</h1>
                    <p class="font-label-stat text-label-stat text-on-surface-variant">Pesanan {{ $dispute->order?->order_no }} · {{ $dispute->order?->gameAccount?->title }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-full font-badge text-badge font-semibold
                    {{ $dispute->status->value === 'resolved' ? 'bg-tertiary-container/30 text-tertiary' : 'bg-error-container/30 text-error' }}">
                    {{ $dispute->status->label() }}
                </span>
                <span class="px-3 py-1.5 rounded-full bg-surface-container text-on-surface-variant font-badge text-badge font-semibold">{{ $dispute->category_label }}</span>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 rounded-3xl bg-surface-container-low border border-outline-variant/20 flex flex-col overflow-hidden">
                <div class="px-5 py-3.5 border-b border-outline-variant/20 flex items-center justify-between">
                    <span class="font-body-sm text-body-sm font-semibold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-error text-lg">support_agent</span>Percakapan Kasus Dispute
                    </span>
                    <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider">{{ $dispute->messages()->count() }} pesan</span>
                </div>

                <div class="flex-1 min-h-[380px] max-h-[520px] overflow-y-auto p-5 space-y-4 chat-scroll" id="chatBox">
                    @forelse($dispute->messages()->get() as $message)
                        @php $mine = $message->sender_id === auth()->id(); $isStaff = $message->role === 'staff'; @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%]">
                                <div class="flex items-center gap-2 mb-1 {{ $mine ? 'justify-end' : '' }}">
                                    @if($isStaff && !$mine)
                                        <span class="material-symbols-outlined text-primary text-sm">verified_user</span>
                                    @elseif(!$mine)
                                        <x-avatar :user="$message->sender" class="w-5 h-5 rounded-full"/>
                                    @endif
                                    <span class="font-label-stat text-label-stat {{ $isStaff ? 'text-primary' : 'text-on-surface-variant' }}">{{ $isStaff ? 'Tim Rekber' : ($mine ? 'Kamu' : $message->sender->username) }}</span>
                                    <span class="font-label-stat text-[10px] text-outline">{{ $message->created_at->format('H:i') }}</span>
                                </div>
                                <div class="rounded-2xl px-4 py-3 font-body-sm text-body-sm leading-relaxed {{ $isStaff ? 'bg-primary-container/30 text-on-surface' : ($mine ? 'bg-secondary-container/40 text-on-surface' : 'bg-surface-container-high text-on-surface') }}">
                                    {{ $message->body }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <span class="material-symbols-outlined text-5xl text-outline">forum</span>
                            <p class="font-body-md text-body-md text-on-surface-variant mt-3">Belum ada percakapan.</p>
                        </div>
                    @endforelse
                </div>

                @if($dispute->status->value !== 'resolved')
                    <div class="border-t border-outline-variant/20 p-4 bg-surface-container/60">
                        <form method="POST" action="{{ route('disputes.message', $dispute) }}">
                            @csrf
                            <div class="flex items-end gap-3">
                                <textarea name="body" rows="1" required placeholder="{{ auth()->user()->isStaff() ? 'Tulis keputusan / permintaan bukti…' : 'Jelaskan kronologi / unggah bukti di sini…' }}" class="flex-1 rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none"></textarea>
                                <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-5 py-3 rounded-2xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors shrink-0">
                                    <span class="material-symbols-outlined text-lg">send</span>Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="border-t border-outline-variant/20 px-5 py-4 bg-tertiary-container/15 flex items-center gap-3">
                        <span class="material-symbols-outlined text-tertiary">check_circle</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Kasus ditutup: <strong class="text-on-surface">{{ $dispute->resolution === 'buyer_win' ? 'Dana dikembalikan ke buyer' : 'Dana dirilis ke seller' }}</strong></p>
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                    <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Info Kasus</span>
                    <dl class="mt-4 space-y-3 font-body-sm text-body-sm">
                        <div class="flex justify-between"><dt class="text-on-surface-variant">Kategori</dt><dd class="text-on-surface font-medium">{{ $dispute->category_label }}</dd></div>
                        <div class="flex justify-between"><dt class="text-on-surface-variant">Dibuka oleh</dt><dd class="text-on-surface font-medium">{{ $dispute->opened_by === auth()->id() ? 'Kamu' : $dispute->openedBy?->username }}</dd></div>
                        <div class="flex justify-between"><dt class="text-on-surface-variant">Melawan</dt><dd class="text-on-surface font-medium">{{ $dispute->opponent_id === auth()->id() ? 'Kamu' : $dispute->opponent?->username }}</dd></div>
                        <div class="flex justify-between"><dt class="text-on-surface-variant">Status</dt><dd class="text-on-surface font-medium">{{ $dispute->status->label() }}</dd></div>
                    </dl>
                    <div class="mt-4 rounded-2xl bg-surface-container p-4">
                        <span class="font-label-stat text-label-stat text-on-surface-variant uppercase tracking-wider">Deskripsi Awal</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">{{ $dispute->description }}</p>
                    </div>
                </div>

                @if(auth()->user()->isStaff() && $dispute->status->value !== 'resolved')
                    <div class="rounded-3xl bg-primary-container/15 border border-primary/40 p-5" x-data="{ decision: 'buyer_win' }">
                        <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">verified_user</span>Putuskan Kasus
                        </h2>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Mutu transaksi escrow Rp {{ number_format($dispute->order?->total_amount ?? 0, 0, ',', '.') }}.</p>
                        <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}" class="mt-4 space-y-3">
                            @csrf
                            <label class="flex items-center gap-2.5 rounded-xl bg-surface-container p-3 cursor-pointer">
                                <input type="radio" name="decision" value="buyer_win" x-model="decision" class="accent-secondary">
                                <span class="font-body-sm text-body-sm text-on-surface">Menang buyer — <span class="text-on-surface-variant">refund dana</span></span>
                            </label>
                            <label class="flex items-center gap-2.5 rounded-xl bg-surface-container p-3 cursor-pointer">
                                <input type="radio" name="decision" value="seller_win" x-model="decision" class="accent-secondary">
                                <span class="font-body-sm text-body-sm text-on-surface">Menang seller — <span class="text-on-surface-variant">rilis dana</span></span>
                            </label>
                            <textarea name="resolution_note" rows="2" required placeholder="Alasan keputusan (dikirim ke kedua pihak)" class="w-full rounded-xl bg-surface-container border border-outline-variant/30 px-3 py-2.5 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none"></textarea>
                            <button type="submit" class="w-full rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-bold py-3 hover:bg-primary transition-colors" onclick="return confirm('Kunci keputusan? Tindakan ini tidak bisa dibatalkan (demo).')">
                                Simpan Keputusan
                            </button>
                        </form>
                    </div>
                @endif

                @if(!auth()->user()->isStaff())
                    <div class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-5">
                        <p class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">schedule</span>Tim rekber akan membalas maksimal 24 jam.
                        </p>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
<style>.chat-scroll::-webkit-scrollbar { width: 6px; } .chat-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 999px; }</style>
<script>
    const chatBox = document.getElementById('chatBox');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
@endpush