<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Models\Game;
use App\Models\GameAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = GameAccount::query()
            ->approved()
            ->with(['game', 'seller.sellerProfile', 'images' => fn ($q) => $q->orderBy('position')->limit(1)])
            ->withCount('images');

        $gameSlug = $request->string('game')->toString();

        if ($gameSlug) {
            $query->whereHas('game', fn ($q) => $q->where('slug', $gameSlug));
        }

        if ($queryString = $request->string('q')->toString()) {
            $query->where(function ($q) use ($queryString) {
                $q->where('title', 'like', "%{$queryString}%")
                    ->orWhere('description', 'like', "%{$queryString}%")
                    ->orWhere('rank', 'like', "%{$queryString}%");
            });
        }

        if ($request->boolean('instant')) {
            $query->where('instant_delivery', true);
        }

        if ($request->filled('min')) {
            $query->where('price', '>=', (int) $request->input('min'));
        }

        if ($request->filled('max')) {
            $query->where('price', '<=', (int) $request->input('max'));
        }

        $sort = $request->string('sort')->toString() ?: 'latest';

        $query->when($sort === 'cheapest', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'expensive', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'popular', fn ($q) => $q->orderByDesc('views_count'))
            ->when(! in_array($sort, ['cheapest', 'expensive', 'popular']), fn ($q) => $q->latest('published_at'));

        $accounts = $query->paginate(12)->withQueryString();

        $games = Game::query()
            ->where('is_active', true)
            ->withCount(['accounts' => fn ($q) => $q->where('status', ListingStatus::Approved->value)])
            ->orderBy('sort_order')
            ->get();

        $activeGame = $gameSlug ? Game::query()->where('slug', $gameSlug)->first() : null;
        $wishlistIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('game_account_id')->flip()
            : collect();

        return view('marketplace.index', compact('accounts', 'games', 'activeGame', 'wishlistIds'));
    }

    public function show(GameAccount $gameAccount): View
    {
        if (! $gameAccount->isApproved()) {
            abort(404);
        }

        $gameAccount->load(['game', 'seller.sellerProfile', 'images' => fn ($q) => $q->orderBy('position')]);
        $gameAccount->increment('views_count');

        $reviews = $gameAccount->seller->receivedReviews()
            ->latest()
            ->with('reviewer')
            ->limit(4)
            ->get();

        $related = GameAccount::query()
            ->approved()
            ->where('game_id', $gameAccount->game_id)
            ->whereKeyNot($gameAccount->getKey())
            ->with(['game', 'seller.sellerProfile'])
            ->limit(4)
            ->get();

        $wishlisted = auth()->check()
            && auth()->user()->wishlists()->where('game_account_id', $gameAccount->id)->exists();

        $gallery = $gameAccount->images->map(fn ($image) => [
            'url' => $image->url(),
            'caption' => $image->caption ?: 'Foto',
        ])->values()->all();

        return view('marketplace.show', compact('gameAccount', 'reviews', 'related', 'wishlisted', 'gallery'));
    }
}
