<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GameVault - Marketplace akun game terpercaya dengan sistem rekber otomatis & garansi uang kembali 100%.">
    <title>@yield('title', 'GameVault - Temukan Akun Game Impianmu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-background font-body-md text-on-surface selection:bg-primary-container selection:text-on-primary-container antialiased" data-auth="{{ auth()->check() ? 'true' : 'false' }}">
    @php
        $navActive = match (request()->route()?->getName()) {
            'home' => 'home',
            'marketplace.index', 'marketplace.show' => 'browse',
            'store.show' => 'browse',
            'wishlist.index' => 'wishlist',
            'cart.index' => 'cart',
            'orders.index', 'orders.show', 'orders.handover' => 'orders',
            'notifications.index' => 'notifications',
            'wallet.index', 'wallet.withdrawals' => 'wallet',
            'seller.*' => 'seller',
            'admin.*' => 'admin',
            default => null,
        };
    @endphp

    @include('components.navbar', ['active' => $navActive, 'compact' => $compact ?? false])

    <main class="w-full min-h-screen bg-surface {{ ($compact ?? false) ? '' : 'pt-28 lg:pt-32' }}">
        @yield('content')
    </main>

    @include('components.footer')

    <div class="fixed bottom-6 right-6 z-50 translate-y-20 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 px-4 py-3 rounded-xl bg-surface-container-highest text-on-surface shadow-2xl" id="toastNotification">
        <span class="material-symbols-outlined text-tertiary" id="toastIcon">check_circle</span>
        <span id="toastText">Berhasil</span>
    </div>

    @stack('scripts')
</body>
</html>