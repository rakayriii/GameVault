<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/accounts/{gameAccount}', [MarketplaceController::class, 'show'])->name('marketplace.show');
Route::get('/store/{store}', [StoreController::class, 'show'])->name('store.show');
Route::get('/help', [HelpController::class, 'index'])->name('help.index');

/*
|--------------------------------------------------------------------------
| Guest authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/2fa/verify', [TwoFactorController::class, 'create'])->name('two-factor.login');
    Route::post('/2fa/verify', [TwoFactorController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::post('/wishlist/{gameAccount:id}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{gameAccount}/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{gameAccount}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout/{gameAccount}', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/{gameAccount}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/{order}/mark-paid', [CheckoutController::class, 'markPaid'])->name('checkout.mark-paid');
    Route::post('/checkout/{order}/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/handover', [OrderController::class, 'handover'])->name('orders.handover');
    Route::post('/orders/{order}/handover/message', [OrderController::class, 'sendMessage'])->name('orders.message');
    Route::post('/orders/{order}/review', [OrderController::class, 'review'])->name('orders.review');
    Route::post('/orders/{order}/dispute', [DisputeController::class, 'store'])->name('orders.dispute');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::get('/wallet/withdrawals', [WalletController::class, 'withdrawals'])->name('wallet.withdrawals');
    Route::post('/wallet/payout-account', [WalletController::class, 'storePayoutAccount'])->name('wallet.payout-account');

    Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{dispute}', [DisputeController::class, 'show'])->name('disputes.show');
    Route::post('/disputes/{dispute}/message', [DisputeController::class, 'storeMessage'])->name('disputes.message');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/seller/request', [SellerController::class, 'requestForm'])->name('seller.request');
    Route::post('/seller/request', [SellerController::class, 'submitRequest'])->name('seller.request.submit');

    Route::prefix('seller')->middleware('seller')->group(function () {
        Route::get('/', [SellerController::class, 'dashboard'])->name('seller.dashboard');
        Route::get('/accounts', [SellerController::class, 'accounts'])->name('seller.accounts');
        Route::get('/accounts/create', [SellerController::class, 'create'])->name('seller.accounts.create');
        Route::post('/accounts', [SellerController::class, 'store'])->name('seller.accounts.store');
        Route::get('/accounts/{gameAccount}/edit', [SellerController::class, 'edit'])->name('seller.accounts.edit');
        Route::put('/accounts/{gameAccount}', [SellerController::class, 'update'])->name('seller.accounts.update');
        Route::delete('/accounts/{gameAccount}', [SellerController::class, 'destroy'])->name('seller.accounts.destroy');
        Route::post('/accounts/{gameAccount}/toggle', [SellerController::class, 'toggle'])->name('seller.accounts.toggle');
        Route::delete('/accounts/images/{accountImage}', [SellerController::class, 'destroyImage'])->name('seller.accounts.images.destroy');
        Route::get('/orders', [SellerController::class, 'orders'])->name('seller.orders');
        Route::get('/store', [SellerController::class, 'storeProfile'])->name('seller.store');
        Route::put('/store', [SellerController::class, 'updateStore'])->name('seller.store.update');
        Route::get('/withdrawals', [WalletController::class, 'withdrawals'])->name('seller.withdrawals');
    });

    Route::prefix('admin')->middleware('staff')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
        Route::get('/rekber', [DashboardController::class, 'rekber'])->name('admin.rekber');
        Route::get('/customers', [DashboardController::class, 'customers'])->name('admin.customers');
        Route::get('/accounts', [SellerController::class, 'adminAccounts'])->name('admin.accounts');
        Route::post('/accounts/{gameAccount}/approve', [SellerController::class, 'approve'])->name('admin.accounts.approve');
        Route::post('/accounts/{gameAccount}/reject', [SellerController::class, 'reject'])->name('admin.accounts.reject');
        Route::get('/games', [GameController::class, 'adminIndex'])->name('admin.games');
        Route::get('/games/create', [GameController::class, 'create'])->name('admin.games.create');
        Route::post('/games', [GameController::class, 'store'])->name('admin.games.store');
        Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('admin.games.edit');
        Route::put('/games/{game}', [GameController::class, 'update'])->name('admin.games.update');
        Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('admin.games.destroy');
        Route::post('/games/{game}/toggle', [GameController::class, 'toggle'])->name('admin.games.toggle');
        Route::get('/seller-requests', [SellerController::class, 'adminRequests'])->name('admin.seller-requests');
        Route::post('/seller-requests/{sellerRequest}/review', [SellerController::class, 'reviewRequest'])->name('admin.seller-requests.review');
        Route::get('/disputes', [DisputeController::class, 'admin'])->name('admin.disputes');
        Route::post('/disputes/{dispute}/resolve', [DisputeController::class, 'resolve'])->name('admin.disputes.resolve');
        Route::get('/withdrawals', [WalletController::class, 'adminWithdrawals'])->name('admin.withdrawals');
        Route::post('/withdrawals/{withdrawal}/process', [WalletController::class, 'processWithdrawal'])->name('admin.withdrawals.process');
    });
});
