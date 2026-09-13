<footer class="w-full bg-surface-container-lowest border-t border-outline-variant/20 pt-16 pb-12 mt-20">
    <div class="max-w-[1340px] mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-outline-variant/20">
            <div class="lg:col-span-2 flex flex-col gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl text-primary">videogame_asset</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight font-bold">Game<span class="text-primary">Vault</span></span>
                </a>
                <p class="font-body-sm text-body-sm text-on-surface-variant max-w-sm leading-relaxed">
                    Marketplace akun game terpercaya di Indonesia. Sistem rekber otomatis, verifikasi seller ketat, dan garansi antik-hackback 100% untuk transaksi yang aman.
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-tertiary font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-sm">shield</span>Anti Fraud &amp; Rekber Verified
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-secondary font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-sm">bolt</span>Instant Transfer 10 Menit
                    </span>
                </div>
            </div>

            <div>
                <h4 class="font-headline-sm text-sm font-semibold text-on-surface uppercase tracking-wider mb-4">Kategori Populer</h4>
                <ul class="flex flex-col gap-2.5 font-body-sm text-body-sm text-on-surface-variant">
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Akun Mobile Legends</a></li>
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Akun Valorant</a></li>
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Akun Genshin Impact</a></li>
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Akun Free Fire</a></li>
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors">Steam &amp; CS2</a></li>
                    <li><a href="{{ route('marketplace.index') }}" class="hover:text-primary transition-colors inline-flex items-center gap-1">Semua Game <span class="material-symbols-outlined text-sm">arrow_forward</span></a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-headline-sm text-sm font-semibold text-on-surface uppercase tracking-wider mb-4">Pusat Bantuan &amp; Rekber</h4>
                <ul class="flex flex-col gap-2.5 font-body-sm text-body-sm text-on-surface-variant">
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Cara Kerja Escrow Rekber</a></li>
                    <li><a href="{{ route('seller.accounts.create') }}" class="hover:text-primary transition-colors">Pusat Edukasi Seller</a></li>
                    <li><a href="{{ route('wallet.index') }}" class="hover:text-primary transition-colors">Wallet &amp; Mutasi Rekber</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Kalkulator Biaya Transaksi</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Lapor Fraud / Sengketa</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-headline-sm text-sm font-semibold text-on-surface uppercase tracking-wider mb-4">Legalitas &amp; Keamanan</h4>
                <ul class="flex flex-col gap-2.5 font-body-sm text-body-sm text-on-surface-variant">
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Syarat &amp; Ketentuan Layanan</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Protokol Saldo Escrow</a></li>
                    <li><a href="{{ route('help.index') }}" class="hover:text-primary transition-colors">Hubungi CS 24/7 (Live Agent)</a></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <span class="font-body-sm text-body-sm text-outline mr-1">Metode Pembayaran:</span>
                @foreach (['QRIS Realtime', 'BCA VA', 'Mandiri Livin', 'GoPay', 'OVO', 'DANA', 'ShopeePay'] as $method)
                    <span class="px-2.5 py-1 rounded bg-surface-container font-label-mono text-label-stat text-on-surface font-semibold">{{ $method }}</span>
                @endforeach
            </div>
            <p class="font-body-sm text-body-sm text-outline text-center">© {{ date('Y') }} GameVault Inc. Dilindungi Hak Cipta &amp; Protokol Escrow Indonesia.</p>
        </div>
    </div>
</footer>