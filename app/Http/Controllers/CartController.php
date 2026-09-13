<?php

namespace App\Http\Controllers;

use App\Models\GameAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(): View
    {
        $items = auth()->user()->cartItems()
            ->with(['gameAccount.game', 'gameAccount.seller.sellerProfile'])
            ->latest()
            ->get();

        $total = $items->sum(fn ($item) => $item->gameAccount->price);

        return view('cart.index', compact('items', 'total'));
    }

    public function add(GameAccount $gameAccount): RedirectResponse
    {
        abort_unless($gameAccount->isApproved(), 404);
        abort_if($gameAccount->seller_id === auth()->id(), 403);

        auth()->user()->cartItems()->firstOrCreate(['game_account_id' => $gameAccount->id]);

        return redirect()->route('cart.index')->with('status', 'Akun ditambahkan ke keranjang.');
    }

    public function remove(GameAccount $gameAccount): RedirectResponse
    {
        auth()->user()->cartItems()->where('game_account_id', $gameAccount->id)->delete();

        return back()->with('status', 'Item dihapus dari keranjang.');
    }

    public function clear(): RedirectResponse
    {
        auth()->user()->cartItems()->delete();

        return back()->with('status', 'Keranjang dikosongkan.');
    }
}
