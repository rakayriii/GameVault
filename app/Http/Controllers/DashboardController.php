<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Enums\WithdrawalStatus;
use App\Models\Dispute;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\SellerRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
        }

        if ($user->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        $orders = $user->orders()->with('gameAccount.game', 'seller.sellerProfile')->latest()->limit(8)->get();
        $activeOrders = $user->orders()->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::Escrow, OrderStatus::Handover])->count();
        $wishlistCount = $user->wishlists()->count();
        $walletBalance = $user->wallet?->available_balance;

        return view('dashboard.index', compact('orders', 'activeOrders', 'wishlistCount', 'walletBalance'));
    }

    public function admin(): View
    {
        $stats = [
            'users' => User::query()->count(),
            'buyers' => User::query()->where('role', UserRole::Buyer)->count(),
            'sellers' => User::query()->where('role', UserRole::Seller)->count(),
            'verified_users' => User::query()->where('is_verified', true)->count(),
            'accounts' => GameAccount::query()->count(),
            'live_accounts' => GameAccount::query()->where('status', ListingStatus::Approved)->count(),
            'pending_accounts' => GameAccount::query()->where('status', ListingStatus::PendingReview)->count(),
            'orders' => Order::query()->count(),
            'pending_orders' => Order::query()->where('status', OrderStatus::PendingPayment)->count(),
            'escrow_active' => Order::query()->whereIn('status', [OrderStatus::Escrow, OrderStatus::Handover])->count(),
            'disputed_orders' => Order::query()->where('status', OrderStatus::Disputed)->count(),
            'completed_orders' => Order::query()->where('status', OrderStatus::Completed)->count(),
            'gmv' => Order::query()->where('status', OrderStatus::Completed)->sum('total_amount'),
            'open_disputes' => Dispute::query()->whereNot('status', 'resolved')->count(),
            'pending_seller_requests' => SellerRequest::query()->where('status', 'pending')->count(),
            'pending_withdrawals' => Withdrawal::query()->where('status', WithdrawalStatus::Pending)->count(),
            'wallet_available' => Wallet::query()->sum('available_balance'),
            'wallet_escrow' => Wallet::query()->sum('escrow_balance'),
        ];

        $recentOrders = Order::query()->with('buyer', 'gameAccount.game')->latest()->limit(6)->get();
        $recentAccounts = GameAccount::query()->with('seller')->where('status', ListingStatus::PendingReview)->latest()->limit(6)->get();

        return view('admin.dashboard', ['stats' => $stats, 'recentOrders' => $recentOrders, 'recentAccounts' => $recentAccounts]);
    }

    public function customers(Request $request): View
    {
        $role = $request->string('role')->toString();

        $query = User::query()->with('wallet', 'sellerProfile');

        if ($role !== '' && in_array($role, array_column(UserRole::cases(), 'value'), true)) {
            $query->where('role', $role);
        } else {
            $role = '';
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->trim()->toString();

            $query->where(function ($sub) use ($q) {
                $sub->where('username', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $summary = [
            'total' => User::query()->count(),
            'buyers' => User::query()->where('role', UserRole::Buyer)->count(),
            'sellers' => User::query()->where('role', UserRole::Seller)->count(),
            'verified' => User::query()->where('is_verified', true)->count(),
            'wallets' => Wallet::query()->count(),
            'total_balance' => Wallet::query()->sum('available_balance'),
            'total_escrow' => Wallet::query()->sum('escrow_balance'),
        ];

        return view('admin.customers', compact('users', 'role', 'summary'));
    }

    public function rekber(): View
    {
        $vault = [
            'available' => Wallet::query()->sum('available_balance'),
            'escrow' => Wallet::query()->sum('escrow_balance'),
            'total' => Wallet::query()->sum('available_balance') + Wallet::query()->sum('escrow_balance'),
            'wallets' => Wallet::query()->count(),
            'pending_count' => Order::query()->where('status', OrderStatus::PendingPayment)->count(),
            'pending_value' => Order::query()->where('status', OrderStatus::PendingPayment)->sum('total_amount'),
            'escrow_count' => Order::query()->whereIn('status', [OrderStatus::Escrow, OrderStatus::Handover])->count(),
            'escrow_value' => Order::query()->whereIn('status', [OrderStatus::Escrow, OrderStatus::Handover])->sum('total_amount'),
        ];

        $inflight = Order::query()
            ->whereIn('status', [OrderStatus::PendingPayment, OrderStatus::Escrow, OrderStatus::Handover])
            ->with(['buyer', 'seller', 'gameAccount.game', 'payments'])
            ->latest()
            ->paginate(15);

        $transactions = WalletTransaction::query()
            ->with('wallet.user')
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.rekber', compact('vault', 'inflight', 'transactions'));
    }
}
