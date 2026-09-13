<?php

namespace App\Http\Controllers;

use App\Models\GameAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class WishlistController extends Controller
{
    public function index(): View
    {
        $items = auth()->user()->wishlists()
            ->with(['gameAccount.game', 'gameAccount.seller.sellerProfile'])
            ->latest()
            ->get();

        return view('wishlist.index', ['items' => $items]);
    }

    public function toggle(GameAccount $gameAccount): RedirectResponse|JsonResponse
    {
        if ($gameAccount->seller_id === auth()->id()) {
            return request()->expectsJson()
                ? response()->json(['ok' => false])
                : back()->with('error', 'Kamu tidak bisa menyimpan akunmu sendiri.');
        }

        $wishlist = auth()->user()->wishlists()->where('game_account_id', $gameAccount->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $added = false;
        } else {
            auth()->user()->wishlists()->create(['game_account_id' => $gameAccount->id]);
            $added = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'added' => $added]);
        }

        return back()->with('status', $added ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.');
    }
}
