<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Models\Game;
use App\Models\GameAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = GameAccount::query()
            ->where('is_featured', true)
            ->approved()
            ->with(['game', 'seller.sellerProfile'])
            ->withCount('images')
            ->latest()
            ->limit(6)
            ->get();

        $trending = GameAccount::query()
            ->approved()
            ->with(['game', 'seller.sellerProfile'])
            ->withCount('images')
            ->orderByDesc('views_count')
            ->limit(8)
            ->get();

        $games = Game::query()
            ->where('is_active', true)
            ->withCount(['accounts' => fn ($q) => $q->where('status', ListingStatus::Approved->value)])
            ->orderBy('sort_order')
            ->get();

        $topSellers = DB::table('seller_profiles')
            ->join('users', 'users.id', '=', 'seller_profiles.user_id')
            ->orderByDesc('seller_profiles.total_sales')
            ->limit(4)
            ->get(['users.id', 'users.username', 'users.is_verified', 'seller_profiles.slug', 'seller_profiles.store_name', 'seller_profiles.rating_cache', 'seller_profiles.total_sales', 'seller_profiles.banner_url']);

        $stats = [
            ['icon' => 'group', 'value' => 'Emerald +', 'label' => 'Community Members', 'accent' => 'text-primary'],
            ['icon' => 'verified_user', 'value' => '8.2K+', 'label' => 'Verified Transaksi', 'accent' => 'text-tertiary'],
            ['icon' => 'xray', 'value' => '10 Min', 'label' => 'Avg. Handover Time', 'accent' => 'text-secondary'],
            ['icon' => 'account_balance_wallet', 'value' => 'Rp2.5B+', 'label' => 'Dana Escrow Terlindungi', 'accent' => 'text-tertiary'],
        ];

        $steps = [
            ['icon' => 'add_circle', 'title' => '1. Buat Akun & Pilih Game', 'desc' => 'Daftar gratis lalu pilih game favoritmu.', 'color' => 'text-primary', 'bg' => 'bg-primary-container/20'],
            ['icon' => 'ads_click', 'title' => '2. Pilih Akun Unggulan', 'desc' => 'Cari akun sesuai budget & kebutuhan gameplay-mu.', 'color' => 'text-secondary', 'bg' => 'bg-secondary-container/20'],
            ['icon' => 'shield', 'title' => '3. Rekber Otomatis Aktif', 'desc' => 'Dana di-hold aman oleh GameVault Escrow.', 'color' => 'text-tertiary', 'bg' => 'bg-tertiary-container/20'],
            ['icon' => 'verified', 'title' => '4. Handover & Mulai Main', 'desc' => 'Terima akun di live room, konfirmasi, main!', 'color' => 'text-primary', 'bg' => 'bg-primary-container/20'],
        ];

        return view('home', compact('featured', 'trending', 'games', 'topSellers', 'stats', 'steps'));
    }
}
