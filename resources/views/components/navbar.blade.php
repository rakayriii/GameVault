@props(['active' => null, 'compact' => false])

<header class="fixed top-0 left-0 w-full z-50 bg-surface/85 backdrop-blur-xl shadow-[0_10px_30px_-10px_rgba(0,0,0,0.5)]" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
    <div class="h-20 max-w-[1340px] mx-auto px-6 lg:px-8 flex items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0">
                <span class="material-symbols-outlined text-3xl text-primary transition-transform group-hover:scale-110">videogame_asset</span>
                <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight font-bold">
                    Game<span class="text-primary">Vault</span>
                </span>
                @auth
                    <span class="hidden lg:inline-flex px-2 py-0.5 rounded-full bg-primary-container/20 text-secondary font-badge text-badge tracking-wider uppercase font-semibold">PRO</span>
                @endauth
            </a>

            <nav class="hidden xl:flex items-center gap-1.5">
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg font-body-sm text-body-sm {{ $active === 'home' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }} transition-all">{{ __('Beranda') }}</a>
                <a href="{{ route('marketplace.index') }}" class="px-3.5 py-2 rounded-lg font-body-sm text-body-sm {{ $active === 'browse' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }} transition-all">{{ __('Browse Accounts') }}</a>
                <a href="{{ route('seller.accounts.create') }}" class="px-3.5 py-2 rounded-lg font-body-sm text-body-sm {{ $active === 'sell' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }} transition-all">{{ __('Jual Akun') }}</a>
                <a href="{{ route('help.index') }}" class="px-3.5 py-2 rounded-lg font-body-sm text-body-sm {{ $active === 'how-it-works' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high' }} transition-all">{{ __('Cara Kerja Rekber') }}</a>
            </nav>
        </div>

        <form action="{{ route('marketplace.index') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-2">
            <div class="relative w-full flex items-center bg-surface-container-low rounded-xl px-3.5 py-2 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container transition-all">
                <span class="material-symbols-outlined text-outline text-xl mr-2.5 select-none">search</span>
                <input name="q" value="{{ request('q') }}" type="text" placeholder="Cari skin Mythic, Valorant Immortal, CS2, Genshin..." class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none">
                <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 rounded bg-surface-container-highest text-on-surface-variant font-label-mono text-label-stat">⌘K</kbd>
            </div>
        </form>

        <div class="flex items-center gap-1">
            @guest
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2.5 rounded-xl bg-surface-container text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high font-body-sm text-body-sm font-semibold transition-all">{{ __('Masuk') }}</a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-all shadow-[0_4px_16px_rgba(160,120,255,0.35)]">{{ __('Daftar') }}</a>
            @endguest

            @auth
                <a href="{{ route('wishlist.index') }}" aria-label="Wishlist" class="relative p-2.5 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[20px]">favorite</span>
                    @if(($wishlistCount ?? 0) > 0)
                        <span class="absolute top-1.5 right-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-secondary-container px-1 font-label-stat text-label-stat text-on-secondary-container font-bold">{{ $wishlistCount }}</span>
                    @endif
                </a>
                <a href="{{ route('notifications.index') }}" aria-label="Notifikasi" class="relative p-2.5 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    @if(($notificationCount ?? 0) > 0)
                        <span class="absolute top-1.5 right-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-error text-on-error px-1 font-label-stat text-label-stat font-bold">{{ $notificationCount }}</span>
                    @endif
                </a>

                @if(auth()->user()->wallet)
                    <a href="{{ route('wallet.index') }}" class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-container-low">
                        <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider">Saldo Rekber</span>
                        <span class="font-label-mono text-label-mono font-semibold text-tertiary">{{ 'Rp ' . number_format(auth()->user()->wallet->available_balance, 0, ',', '.') }}</span>
                        <span class="h-6 w-6 rounded-lg bg-surface-container-high text-secondary hover:bg-secondary hover:text-on-secondary flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-sm">add</span>
                        </span>
                    </a>
                @endif

                <a href="{{ route('seller.accounts.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-all shadow-[0_4px_16px_rgba(160,120,255,0.35)] hover:shadow-[0_6px_22px_rgba(160,120,255,0.5)]">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>Jual Akun</span>
                </a>

                <div class="hidden md:flex items-center gap-2 pl-2 border-l border-outline-variant/30" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open" class="relative flex items-center gap-2 p-1.5 rounded-xl hover:bg-surface-container-highest transition-colors cursor-pointer">
                        <span class="relative">
                            <x-avatar :user="auth()->user()" class="w-8 h-8 rounded-full text-xs" />
                            <span class="absolute -bottom-0.5 -right-0.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-surface">
                                <span class="block h-2.5 w-2.5 rounded-full {{ auth()->user()->isSeller() ? 'bg-tertiary' : 'bg-secondary' }}"></span>
                            </span>
                        </span>
                        <span class="hidden lg:flex flex-col items-start leading-tight">
                            <span class="font-body-sm text-body-sm font-semibold text-on-surface flex items-center gap-1">
                                {{ auth()->user()->username }}
                                @if(auth()->user()->is_verified)
                                    <span class="material-symbols-outlined text-secondary text-xs">verified</span>
                                @endif
                            </span>
                            <span class="font-label-stat text-[10px] text-on-surface-variant uppercase">{{ auth()->user()->role_label }}</span>
                        </span>
                        <span class="material-symbols-outlined text-on-surface-variant text-sm">expand_more</span>
                    </button>
                    <div x-show="open" x-transition.opacity class="absolute top-16 right-0 mt-1 w-56 rounded-2xl bg-surface-container-low p-2 shadow-2xl ring-1 ring-outline-variant/30 z-50">
                        <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-surface-container text-on-surface font-body-sm text-body-sm transition-colors">
                            <span class="material-symbols-outlined text-secondary text-lg">shopping_bag</span>Pesanan Saya
                        </a>
                        @if(auth()->user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-surface-container text-on-surface font-body-sm text-body-sm transition-colors">
                                <span class="material-symbols-outlined text-secondary text-lg">storefront</span>Seller Center
                            </a>
                        @else
                            <a href="{{ route('seller.request') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-surface-container text-on-surface font-body-sm text-body-sm transition-colors">
                                <span class="material-symbols-outlined text-secondary text-lg">storefront</span>Jadi Penjual
                            </a>
                        @endif
                        <a href="{{ route('wallet.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-surface-container text-on-surface font-body-sm text-body-sm transition-colors">
                            <span class="material-symbols-outlined text-secondary text-lg">account_balance_wallet</span>Wallet Rekber
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-surface-container text-on-surface font-body-sm text-body-sm transition-colors">
                            <span class="material-symbols-outlined text-secondary text-lg">chat</span>Pesan & Chat
                        </a>
                        @if(auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-surface-container text-on-surface font-body-sm text-body-sm transition-colors">
                                <span class="material-symbols-outlined text-primary text-lg">admin_panel_settings</span>Admin Console
                            </a>
                        @endif
                        <div class="my-2 h-px bg-outline-variant/30"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-error-container/40 text-error font-body-sm text-body-sm transition-colors">
                                <span class="material-symbols-outlined text-lg">logout</span>Keluar
                            </button>
                        </form>
                    </div>
                </div>

                <button type="button" class="md:hidden p-2.5 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors cursor-pointer" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-label="Buka menu navigasi">
                    <span class="material-symbols-outlined text-[22px]" x-show="!mobileOpen" x-cloak>menu</span>
                    <span class="material-symbols-outlined text-[22px]" x-show="mobileOpen" x-cloak>close</span>
                </button>
            @endauth
        </div>
    </div>

    {{-- Mobile navigation drawer --}}
    <div x-show="mobileOpen" x-cloak class="md:hidden bg-surface border-t border-outline-variant/20 shadow-xl">
        <div class="px-6 py-4 space-y-1 max-h-[calc(100dvh-10rem)] overflow-y-auto">
            <form action="{{ route('marketplace.index') }}" method="GET" class="mb-3">
                <div class="relative flex items-center bg-surface-container-low rounded-xl px-3.5 py-2.5 text-on-surface-variant focus-within:text-on-surface focus-within:bg-surface-container transition-all">
                    <span class="material-symbols-outlined text-outline text-xl mr-2.5 select-none">search</span>
                    <input name="q" value="{{ request('q') }}" type="text" placeholder="Cari skin Mythic, Valorant, CS2..." class="w-full bg-transparent font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none">
                </div>
            </form>
            <a href="{{ route('home') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $active === 'home' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }} font-body-sm text-body-sm transition-colors">
                <span class="material-symbols-outlined text-lg text-secondary shrink-0">home</span>{{ __('Beranda') }}
            </a>
            <a href="{{ route('marketplace.index') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $active === 'browse' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }} font-body-sm text-body-sm transition-colors">
                <span class="material-symbols-outlined text-lg text-secondary shrink-0">sell</span>{{ __('Browse Accounts') }}
            </a>
            <a href="{{ route('seller.accounts.create') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $active === 'sell' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }} font-body-sm text-body-sm transition-colors">
                <span class="material-symbols-outlined text-lg text-secondary shrink-0">add_circle</span>{{ __('Jual Akun') }}
            </a>
            <a href="{{ route('help.index') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ $active === 'how-it-works' ? 'bg-surface-container-high text-on-surface font-semibold' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }} font-body-sm text-body-sm transition-colors">
                <span class="material-symbols-outlined text-lg text-secondary shrink-0">handshake</span>{{ __('Cara Kerja Rekber') }}
            </a>

            @auth
                <div class="my-2 h-px bg-outline-variant/30"></div>
                <a href="{{ route('orders.index') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-body-sm text-body-sm transition-colors">
                    <span class="material-symbols-outlined text-lg text-secondary shrink-0">shopping_bag</span>Pesanan Saya
                </a>
                @if(auth()->user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-body-sm text-body-sm transition-colors">
                        <span class="material-symbols-outlined text-lg text-secondary shrink-0">storefront</span>Seller Center
                    </a>
                @else
                    <a href="{{ route('seller.request') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-body-sm text-body-sm transition-colors">
                        <span class="material-symbols-outlined text-lg text-secondary shrink-0">storefront</span>Jadi Penjual
                    </a>
                @endif
                <a href="{{ route('wallet.index') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-body-sm text-body-sm transition-colors">
                    <span class="material-symbols-outlined text-lg text-secondary shrink-0">account_balance_wallet</span>Wallet Rekber
                </a>
                @if(auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-body-sm text-body-sm transition-colors">
                        <span class="material-symbols-outlined text-lg text-primary shrink-0">admin_panel_settings</span>Admin Console
                    </a>
                @endif
                <div class="my-2 h-px bg-outline-variant/30"></div>
                <form method="POST" action="{{ route('logout') }}" class="px-3">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 py-2.5 rounded-xl text-error hover:bg-error-container/40 font-body-sm text-body-sm transition-colors text-left cursor-pointer">
                        <span class="material-symbols-outlined text-lg shrink-0">logout</span>Keluar
                    </button>
                </form>
            @else
                <div class="my-2 h-px bg-outline-variant/30"></div>
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('login') }}" @click="mobileOpen = false" class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-surface-container text-on-surface font-body-sm text-body-sm font-semibold hover:bg-surface-container-high transition-colors">{{ __('Masuk') }}</a>
                    <a href="{{ route('register') }}" @click="mobileOpen = false" class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-primary-container text-on-primary-container font-body-sm text-body-sm font-semibold hover:bg-primary transition-colors">{{ __('Daftar') }}</a>
                </div>
            @endauth
        </div>
    </div>

    {{-- Trust strip --}}
    <div class="w-full bg-surface-container-lowest/90 border-t border-outline-variant/20 overflow-hidden">
        <div class="max-w-[1340px] mx-auto px-6 lg:px-8 py-2.5 flex items-center gap-4 overflow-x-auto">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary-container/20 text-tertiary font-label-stat text-label-stat font-semibold tracking-wide shrink-0">
                <span class="material-symbols-outlined text-sm text-tertiary">verified_user</span>
                <span>GARANSI REKBER 100% AMAN &amp; ANTI HACKBACK</span>
            </div>
            <span class="font-label-stat text-label-stat text-outline uppercase tracking-wider shrink-0">Top Games:</span>
            @forelse($topGames ?? [] as $game)
                <a href="{{ route('marketplace.index', ['game' => $game->slug]) }}" class="px-2.5 py-1 rounded-full bg-surface-container hover:bg-surface-container-high hover:text-secondary transition-colors font-body-sm text-body-sm text-on-surface-variant shrink-0">
                    {{ $game->name }}
                </a>
            @empty
                <a href="{{ route('marketplace.index') }}" class="px-2.5 py-1 rounded-full bg-surface-container hover:bg-surface-container-high hover:text-secondary transition-colors font-body-sm text-body-sm text-on-surface-variant shrink-0">Mobile Legends</a>
                <a href="{{ route('marketplace.index') }}" class="px-2.5 py-1 rounded-full bg-surface-container hover:bg-surface-container-high hover:text-secondary transition-colors font-body-sm text-body-sm text-on-surface-variant shrink-0">Valorant</a>
                <a href="{{ route('marketplace.index') }}" class="px-2.5 py-1 rounded-full bg-surface-container hover:bg-surface-container-high hover:text-secondary transition-colors font-body-sm text-body-sm text-on-surface-variant shrink-0">Free Fire</a>
            @endforelse
        </div>
    </div>
</header>