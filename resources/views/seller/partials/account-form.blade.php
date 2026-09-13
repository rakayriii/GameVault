@props(['account' => null, 'games' => []])

<form method="POST" enctype="multipart/form-data" action="{{ ($account ?? null) ? route('seller.accounts.update', $account) : route('seller.accounts.store') }}" class="rounded-3xl bg-surface-container-low border border-outline-variant/20 p-7 space-y-6" x-data="accountImages()" @submit="syncImages()">
    @csrf
    @if($account)
        @method('PUT')
    @endif

    <div class="grid md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Judul Listing</label>
            <input name="title" value="{{ old('title', $account?->title) }}" required maxlength="120" placeholder="Ex: MLBB ID 8912455 - Exp 99, Hero 118" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
            @error('title') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Game</label>
            <select name="game_id" required class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
                @foreach($games as $game)
                    <option value="{{ $game->id }}" @selected((int) old('game_id', $account?->game_id) === $game->id)>{{ $game->name }}</option>
                @endforeach
            </select>
            @error('game_id') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Harga Jual (Rp)</label>
            <input type="number" name="price" value="{{ old('price', $account?->price) }}" required min="10000" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface focus:outline-none focus:border-primary">
            @error('price') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Harga Coret (Rp) <span class="text-outline">(opsional)</span></label>
            <input type="number" name="strike_price" value="{{ old('strike_price', $account?->strike_price) }}" min="0" placeholder="Ex: 8500000" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
        </div>

        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Diskon % <span class="text-outline">(opsional)</span></label>
            <input type="number" name="discount_percent" value="{{ old('discount_percent', $account?->discount_percent) }}" min="1" max="90" placeholder="Ex: 20" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-label-mono text-label-mono text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
        </div>

        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Server</label>
            <input name="server" value="{{ old('server', $account?->server) }}" placeholder="Ex: 2250" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Region</label>
            <input name="region" value="{{ old('region', $account?->region) }}" placeholder="Ex: Indonesia" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Rank</label>
            <input name="rank" value="{{ old('rank', $account?->rank) }}" placeholder="Ex: Mythical Honor" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Tier / Bintang</label>
            <input name="rank_tier" value="{{ old('rank_tier', $account?->rank_tier) }}" placeholder="Ex: 50 Stars" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Level</label>
            <input type="number" name="level" value="{{ old('level', $account?->level) }}" min="1" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Jumlah Hero</label>
            <input type="number" name="heros_count" value="{{ old('heros_count', $account?->heros_count) }}" min="0" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Jumlah Skin</label>
            <input type="number" name="skins_count" value="{{ old('skins_count', $account?->skins_count) }}" min="0" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Winrate (%)</label>
            <input type="number" step="0.1" name="winrate" value="{{ old('winrate', $account?->winrate) }}" min="0" max="100" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Saldo In-game</label>
            <input type="number" name="in_game_balance" value="{{ old('in_game_balance', $account?->in_game_balance) }}" min="0" class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary">
        </div>
    </div>

    <div>
        <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Deskripsi</label>
        <textarea name="description" required rows="4" maxlength="2000" placeholder="Detail kondisi akun, jumlah hero/skin legendary, status bind, dsb." class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none">{{ old('description', $account?->description) }}</textarea>
        @error('description') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="font-body-sm text-body-sm text-on-surface-variant mb-1.5 block">Catatan Handover <span class="text-outline">(opsional, hanya seller & tim rekber yang melihat)</span></label>
        <textarea name="handover_note" rows="2" maxlength="1000" placeholder="Ex: Buka email bind blabla@gmail.com lalu ganti password via menu keamanan." class="w-full rounded-2xl bg-surface-container border border-outline-variant/30 px-4 py-3 font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary resize-none">{{ old('handover_note', $account?->handover_note) }}</textarea>
    </div>

    <label class="flex items-center gap-3 rounded-2xl bg-surface-container p-4 cursor-pointer">
        <input type="checkbox" name="instant_delivery" value="1" class="h-5 w-5 rounded accent-secondary" @checked((bool) old('instant_delivery', $account?->instant_delivery))>
        <span class="font-body-sm text-body-sm text-on-surface"><strong>Pengiriman instan</strong> <span class="text-on-surface-variant">— kredensial dikirim langsung setelah pembayaran.</span></span>
    </label>

    <div class="rounded-2xl bg-surface-container border border-outline-variant/30 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <label class="font-body-sm text-body-sm text-on-surface font-semibold block">Screenshot Akun</label>
                <span class="font-label-stat text-label-stat text-on-surface-variant">Tambah hingga 8 gambar. Buyer &amp; tim rekber akan melihatnya.</span>
            </div>
            <span class="font-label-stat text-label-stat text-outline">{{ $account?->images->count() ?? 0 }} / 8</span>
        </div>

        @if($account && $account->images->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                @foreach($account->images as $index => $image)
                    <div x-ref="img{{ $image->id }}" class="rounded-xl overflow-hidden bg-surface-container-high border border-outline-variant/20 relative group">
                        <img src="{{ $image->url() }}" alt="{{ $image->caption ?: 'Screenshot '.($index + 1) }}" class="w-full h-28 object-cover">
                        <label @change="$refs.img{{ $image->id }}.classList.toggle('opacity-40'); $el.nextElementSibling.textContent = $el.checked ? 'Batal' : 'Hapus'" class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-lg bg-surface-container-lowest/90 text-error font-label-stat text-[10px] font-semibold cursor-pointer flex items-center gap-1 shadow select-none" title="Centang untuk menghapus foto ini saat disimpan">
                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="accent-error">
                            <span class="pointer-events-none">Hapus</span>
                        </label>
                        <input name="captions[{{ $image->id }}]" value="{{ $image->caption }}" placeholder="Keterangan (opsional)" class="w-full px-2.5 py-1.5 text-[12px] bg-surface-container border-t border-outline-variant/20 text-on-surface placeholder:text-outline focus:outline-none">
                    </div>
                @endforeach
            </div>
        @endif

        <template x-if="previews.length">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                <template x-for="(preview, i) in previews" :key="i">
                    <div class="rounded-xl overflow-hidden bg-surface-container-high border border-outline-variant/20 relative">
                        <img :src="preview.url" class="w-full h-28 object-cover" alt="Pratinjau">
                        <button type="button" @click="removePreview(i)" class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-lg bg-surface-container-lowest/90 text-error font-label-stat text-[10px] font-semibold cursor-pointer flex items-center gap-1 shadow">Hapus</button>
                        <input type="text" name="new_captions[]" x-model="preview.caption" placeholder="Keterangan (opsional)" class="w-full px-2.5 py-1.5 text-[12px] bg-surface-container border-t border-outline-variant/20 text-on-surface placeholder:text-outline focus:outline-none">
                    </div>
                </template>
            </div>
        </template>

        <label class="flex items-center justify-center gap-2 w-full py-4 rounded-xl border-2 border-dashed border-outline-variant/40 hover:border-secondary/60 hover:bg-surface-container-high/60 cursor-pointer transition-colors">
            <input type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" @change="handleFiles($event)">
            <span class="material-symbols-outlined text-secondary">add_photo_alternate</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant font-semibold">Pilih file gambar / screenshot</span>
        </label>
        <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden" x-ref="fileInput">
        @error('images') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
        @error('images.*') <p class="text-error text-body-sm mt-1.5">{{ $message }}</p> @enderror
    </div>

    @if($account && $account->status->value === 'approved')
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="flex-1 rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold py-3.5 hover:bg-primary transition-colors">Simpan Perubahan (Tetap Live)</button>
            <button type="submit" name="status" value="pending" class="flex-1 rounded-2xl bg-secondary-container/40 text-secondary font-body-sm text-body-sm font-semibold py-3.5 hover:bg-secondary hover:text-on-secondary transition-colors">Kirim Ulang Review</button>
            <button type="submit" name="status" value="draft" class="flex-1 rounded-2xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold py-3.5 hover:bg-surface-container-high transition-colors">Set Draft</button>
        </div>
        <p class="font-label-stat text-[11px] text-on-surface-variant -mt-3">Listing live langsung diperbarui — harga, judul, deskripsi, dan screenshot bisa diubah tanpa menurunkan listing.</p>
    @elseif($account && in_array($account->status->value, ['draft', 'rejected']))
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-2xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold px-6 py-3 hover:bg-primary transition-colors">Simpan & Kirim Review</button>
            <button type="submit" name="status" value="draft" class="rounded-2xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-semibold px-6 py-3 hover:bg-surface-container-high transition-colors">Simpan Draft</button>
        </div>
    @else
        <button type="submit" class="w-full rounded-2xl bg-primary-container text-on-primary-container font-body-md text-body-md font-bold py-3.5 hover:bg-primary transition-colors">Kirim Untuk Review Admin</button>
    @endif
</form>

@push('scripts')
<script>
    function accountImages() {
        return {
            previews: [],
            files: [],
            handleFiles(event) {
                const remaining = 8 - this.previews.length;
                Array.from(event.target.files || [])
                    .filter((file) => file.type.startsWith('image/'))
                    .slice(0, remaining)
                    .forEach((file) => {
                        this.files.push(file);
                        this.previews.push({ url: URL.createObjectURL(file), caption: '' });
                    });
                event.target.value = '';
            },
            removePreview(index) {
                this.previews.splice(index, 1);
                this.files.splice(index, 1);
            },
            syncImages() {
                const transfer = new DataTransfer();
                this.files.forEach((file) => transfer.items.add(file));
                this.$refs.fileInput.files = transfer.files;
            },
        };
    }
</script>
@endpush