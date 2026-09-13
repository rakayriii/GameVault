<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use Illuminate\Contracts\View\View;

class StoreController extends Controller
{
    public function show(SellerProfile $store): View
    {
        $store->loadCount('followers')->load(['seller', 'accounts' => fn ($q) => $q->approved()->with(['game'])->latest('published_at')]);

        $stats = [
            ['icon' => 'confirm_number', 'value' => $store->total_sales, 'label' => 'Total Transaksi', 'accent' => 'text-primary'],
            ['icon' => 'star', 'value' => number_format($store->rating_cache, 1, ',', '.'), 'label' => 'Rating', 'accent' => 'text-secondary'],
            ['icon' => 'schedule', 'value' => $store->response_time_minutes.' Min', 'label' => 'Respon', 'accent' => 'text-tertiary'],
            ['icon' => 'groups', 'value' => $store->followers_count ?? 0, 'label' => 'Pengikut', 'accent' => 'text-tertiary'],
        ];

        return view('store.show', compact('store', 'stats'));
    }
}
