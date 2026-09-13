<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\SellerRequestStatus;
use App\Models\AccountImage;
use App\Models\Game;
use App\Models\GameAccount;
use App\Models\SellerProfile;
use App\Models\SellerRequest;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();

        $stats = [
            'total_accounts' => $user->gameAccounts()->count(),
            'active_accounts' => $user->gameAccounts()->where('status', ListingStatus::Approved)->count(),
            'pending_accounts' => $user->gameAccounts()->where('status', ListingStatus::PendingReview)->count(),
            'total_sales' => $user->sales()->where('status', OrderStatus::Completed)->count(),
            'revenue' => $user->sales()->where('status', OrderStatus::Completed)->sum('total_amount'),
            'rating' => $user->sellerProfile?->rating_cache,
            'actionable_orders' => $user->sales()
                ->whereIn('status', [OrderStatus::Escrow->value, OrderStatus::Handover->value])
                ->count(),
            'pending_payment_orders' => $user->sales()->where('status', OrderStatus::PendingPayment->value)->count(),
        ];

        $recentOrders = $user->sales()->with('buyer', 'gameAccount.game', 'gameAccount.images')->latest()->limit(8)->get();
        $recentAccounts = $user->gameAccounts()->with('game')->latest()->limit(6)->get();

        return view('seller.dashboard', [...$stats, 'recentOrders' => $recentOrders, 'recentAccounts' => $recentAccounts]);
    }

    public function accounts(): View
    {
        $accounts = auth()->user()->gameAccounts()->with('game')->latest()->paginate(10);

        return view('seller.accounts', compact('accounts'));
    }

    public function create(): View
    {
        $games = Game::query()->where('is_active', true)->orderBy('name')->get();

        return view('seller.accounts-create', compact('games'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'game_id' => ['required', 'exists:games,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:10000', 'max:2000000000'],
            'strike_price' => ['nullable', 'integer', 'gte:price', 'max:2000000000'],
            'discount_percent' => ['nullable', 'integer', 'between:1,90'],
            'server' => ['nullable', 'string', 'max:80'],
            'region' => ['nullable', 'string', 'max:80'],
            'rank' => ['nullable', 'string', 'max:100'],
            'rank_tier' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'heros_count' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'skins_count' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'winrate' => ['nullable', 'numeric', 'between:0,100'],
            'in_game_balance' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'instant_delivery' => ['nullable', 'boolean'],
            'handover_note' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'new_captions' => ['nullable', 'array', 'max:8'],
            'new_captions.*' => ['nullable', 'string', 'max:255'],
        ], [], array_combine(
            ['game_id', 'title', 'description', 'price', 'strike_price', 'discount_percent', 'server', 'region', 'rank', 'rank_tier', 'level', 'heros_count', 'skins_count', 'winrate', 'in_game_balance', 'instant_delivery', 'handover_note', 'images', 'new_captions'],
            ['Game', 'Judul', 'Deskripsi', 'Harga', 'Harga coret', 'Diskon', 'Server', 'Region', 'Rank', 'Tier', 'Level', 'Jumlah hero', 'Jumlah skin', 'Winrate', 'Saldo in-game', 'Pengiriman instan', 'Catatan handover', 'Screenshot akun', 'Keterangan gambar']
        ));

        $game = Game::findOrFail($request->integer('game_id'));

        $account = auth()->user()->gameAccounts()->create([
            'game_id' => $game->id,
            'title' => $request->string('title')->trim()->toString(),
            'slug' => $this->uniqueSlug($request->string('title')->trim()->toString()),
            'description' => $request->string('description')->trim()->toString(),
            'price' => $request->integer('price'),
            'strike_price' => $request->filled('strike_price') ? $request->integer('strike_price') : null,
            'discount_percent' => $request->filled('discount_percent') ? $request->integer('discount_percent') : null,
            'server' => $request->string('server')->trim()->toString() ?: null,
            'region' => $request->string('region')->trim()->toString() ?: null,
            'rank' => $request->string('rank')->trim()->toString() ?: null,
            'rank_tier' => $request->string('rank_tier')->trim()->toString() ?: null,
            'level' => $request->filled('level') ? $request->integer('level') : null,
            'heros_count' => $request->filled('heros_count') ? $request->integer('heros_count') : null,
            'skins_count' => $request->filled('skins_count') ? $request->integer('skins_count') : null,
            'winrate' => $request->filled('winrate') ? $request->float('winrate') : null,
            'in_game_balance' => $request->filled('in_game_balance') ? $request->integer('in_game_balance') : null,
            'instant_delivery' => $request->boolean('instant_delivery'),
            'handover_note' => $request->string('handover_note')->trim()->toString() ?: null,
            'status' => ListingStatus::PendingReview,
            'handover_data' => null,
        ]);

        Notifier::notify(User::where('role', 'admin')->firstOrFail(), 'account', 'Listing baru menunggu review', "{$account->seller->username} mengajukan '{$account->title}'.");

        $this->storeImages($account, $request);

        return redirect()->route('seller.accounts')->with('status', 'Listing dikirim untuk review tim admin. Biasanya selesai < 24 jam.');
    }

    public function edit(GameAccount $gameAccount): View|RedirectResponse
    {
        $this->authorizeOwnership($gameAccount);

        if ($gameAccount->status === ListingStatus::Sold) {
            return redirect()->route('seller.accounts')->with('error', 'Listing yang sudah terjual tidak bisa diedit.');
        }

        $games = Game::query()->where('is_active', true)->orderBy('name')->get();

        return view('seller.accounts-edit', ['account' => $gameAccount, 'games' => $games]);
    }

    public function update(GameAccount $gameAccount, Request $request): RedirectResponse
    {
        $this->authorizeOwnership($gameAccount);

        abort_if($gameAccount->status === ListingStatus::Sold, 403);

        $request->validate([
            'game_id' => ['required', 'exists:games,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:10000', 'max:2000000000'],
            'strike_price' => ['nullable', 'integer', 'gte:price', 'max:2000000000'],
            'discount_percent' => ['nullable', 'integer', 'between:1,90'],
            'server' => ['nullable', 'string', 'max:80'],
            'region' => ['nullable', 'string', 'max:80'],
            'rank' => ['nullable', 'string', 'max:100'],
            'rank_tier' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'heros_count' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'skins_count' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'winrate' => ['nullable', 'numeric', 'between:0,100'],
            'in_game_balance' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'instant_delivery' => ['nullable', 'boolean'],
            'handover_note' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', Rule::in([ListingStatus::PendingReview->value, ListingStatus::Draft->value])],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'new_captions' => ['nullable', 'array', 'max:8'],
            'new_captions.*' => ['nullable', 'string', 'max:255'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:255'],
            'remove_images' => ['nullable', 'array', 'max:8'],
            'remove_images.*' => ['integer', 'exists:account_images,id'],
        ], [], array_combine(
            ['game_id', 'title', 'description', 'price', 'strike_price', 'discount_percent', 'server', 'region', 'rank', 'rank_tier', 'level', 'heros_count', 'skins_count', 'winrate', 'in_game_balance', 'instant_delivery', 'handover_note', 'status', 'images', 'new_captions', 'captions', 'remove_images'],
            ['Game', 'Judul', 'Deskripsi', 'Harga', 'Harga coret', 'Diskon', 'Server', 'Region', 'Rank', 'Tier', 'Level', 'Jumlah hero', 'Jumlah skin', 'Winrate', 'Saldo in-game', 'Pengiriman instan', 'Catatan handover', 'Status', 'Screenshot akun', 'Keterangan gambar', 'Keterangan gambar', 'Gambar yang dihapus']
        ));

        $wasLive = $gameAccount->status === ListingStatus::Approved;
        $requestedStatus = $request->filled('status') ? $request->string('status')->toString() : null;

        $status = $wasLive
            ? match ($requestedStatus) {
                ListingStatus::Draft->value => ListingStatus::Draft,
                ListingStatus::PendingReview->value => ListingStatus::PendingReview,
                default => ListingStatus::Approved,
            }
        : match ($requestedStatus) {
            ListingStatus::Draft->value => ListingStatus::Draft,
            default => ListingStatus::PendingReview,
        };

        $gameAccount->forceFill([
            'game_id' => $request->integer('game_id'),
            'title' => $request->string('title')->trim()->toString(),
            'slug' => $this->uniqueSlug($request->string('title')->trim()->toString(), $gameAccount->id),
            'description' => $request->string('description')->trim()->toString(),
            'price' => $request->integer('price'),
            'strike_price' => $request->filled('strike_price') ? $request->integer('strike_price') : null,
            'discount_percent' => $request->filled('discount_percent') ? $request->integer('discount_percent') : null,
            'server' => $request->string('server')->trim()->toString() ?: null,
            'region' => $request->string('region')->trim()->toString() ?: null,
            'rank' => $request->string('rank')->trim()->toString() ?: null,
            'rank_tier' => $request->string('rank_tier')->trim()->toString() ?: null,
            'level' => $request->filled('level') ? $request->integer('level') : null,
            'heros_count' => $request->filled('heros_count') ? $request->integer('heros_count') : null,
            'skins_count' => $request->filled('skins_count') ? $request->integer('skins_count') : null,
            'winrate' => $request->filled('winrate') ? $request->float('winrate') : null,
            'in_game_balance' => $request->filled('in_game_balance') ? $request->integer('in_game_balance') : null,
            'instant_delivery' => $request->boolean('instant_delivery'),
            'handover_note' => $request->string('handover_note')->trim()->toString() ?: null,
            'status' => $status,
            'rejection_reason' => null,
        ])->save();

        $this->syncImages($gameAccount, $request);

        if ($status === ListingStatus::PendingReview) {
            Notifier::notify(User::where('role', 'admin')->firstOrFail(), 'account', 'Listing diedit & menunggu review', "{$gameAccount->seller->username} memperbarui '{$gameAccount->title}'.");
            $message = 'Listing diperbarui dan dikirim ulang untuk review.';
        } else {
            $message = $status === ListingStatus::Approved
                ? 'Listing live diperbarui tanpa review ulang.'
                : 'Listing tersimpan sebagai draft.';
        }

        return redirect()->route('seller.accounts')->with('status', $message);
    }

    public function destroy(GameAccount $gameAccount): RedirectResponse
    {
        $this->authorizeOwnership($gameAccount);

        $gameAccount->images()->get()->each(fn (AccountImage $image) => $this->deleteImageFile($image));
        $gameAccount->delete();

        return back()->with('status', 'Listing dihapus.');
    }

    public function destroyImage(AccountImage $accountImage): RedirectResponse
    {
        abort_if($accountImage->gameAccount->seller_id !== auth()->id(), 403);

        $this->deleteImageFile($accountImage);
        $accountImage->delete();

        return back()->with('status', 'Screenshot dihapus.');
    }

    public function toggle(GameAccount $gameAccount, Request $request): RedirectResponse
    {
        $this->authorizeOwnership($gameAccount);

        $action = $request->string('action')->toString();

        if ($action === 'offline' && $gameAccount->status === ListingStatus::Approved) {
            $gameAccount->update(['status' => ListingStatus::Draft]);

            return back()->with('status', 'Listing di-nonaktifkan dari marketplace.');
        }

        if ($action === 'online' && $gameAccount->status === ListingStatus::Draft) {
            $gameAccount->update(['status' => ListingStatus::Approved, 'published_at' => now()]);

            return back()->with('status', 'Listing kembali live.');
        }

        return back()->with('error', 'Listing tidak bisa diubah statusnya pada kondisi ini.');
    }

    public function orders(Request $request): View
    {
        $query = auth()->user()->sales()->with('buyer', 'gameAccount.game');

        if ($request->filled('status') && in_array($request->string('status')->toString(), array_column(OrderStatus::cases(), 'value'), true)) {
            $query->where('status', $request->string('status')->toString());
        }

        $orders = $query->latest()->paginate(12);

        return view('seller.orders', compact('orders'));
    }

    public function storeProfile(): View
    {
        $profile = auth()->user()->sellerProfile;

        return view('seller.store', compact('profile'));
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $profile = auth()->user()->sellerProfile;

        $request->validate([
            'store_name' => ['required', 'string', 'max:60'],
            'bio' => ['nullable', 'string', 'max:500'],
            'announcement' => ['nullable', 'string', 'max:255'],
        ], [], ['store_name' => 'Nama toko', 'bio' => 'Bio', 'announcement' => 'Pengumuman']);

        $profile->forceFill([
            'store_name' => $request->string('store_name')->trim()->toString(),
            'bio' => $request->string('bio')->trim()->toString() ?: null,
            'announcement' => $request->string('announcement')->trim()->toString() ?: null,
        ])->save();

        return back()->with('status', 'Profil toko diperbarui.');
    }

    public function requestForm(): View
    {
        $user = auth()->user();

        if ($user->isSeller()) {
            return view('seller.request', ['request' => null, 'alreadySeller' => true]);
        }

        $pendingRequest = $user->sellerRequests()->latest()->first();

        if ($pendingRequest?->status === SellerRequestStatus::Pending) {
            return view('seller.request', ['request' => $pendingRequest, 'alreadySeller' => false]);
        }

        return view('seller.request', ['request' => $pendingRequest, 'alreadySeller' => false]);
    }

    public function submitRequest(Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_if($user->isSeller(), 403);

        if ($user->sellerRequests()->where('status', SellerRequestStatus::Pending)->exists()) {
            return back()->with('error', 'Pengajuan kamu masih dalam proses review.');
        }

        $request->validate([
            'store_name' => ['required', 'string', 'max:60'],
            'reason' => ['required', 'string', 'max:500'],
            'experience' => ['nullable', 'string', 'max:500'],
        ], [], ['store_name' => 'Nama toko', 'reason' => 'Alasan', 'experience' => 'Pengalaman']);

        $sellerRequest = $user->sellerRequests()->create([
            'store_name' => $request->string('store_name')->trim()->toString(),
            'reason' => $request->string('reason')->trim()->toString(),
            'experience' => $request->string('experience')->trim()->toString() ?: null,
            'status' => SellerRequestStatus::Pending,
        ]);

        Notifier::notify(User::where('role', 'admin')->first(), 'seller', 'Pengajuan jadi penjual baru', "{$user->username} ingin membuka toko '{$sellerRequest->store_name}'.");

        return back()->with('status', 'Pengajuan jadi penjual terkirim. Tim admin akan mereview dalam 1×24 jam.');
    }

    public function adminAccounts(Request $request): View
    {
        $tab = $request->string('tab')->toString() ?: 'pending';

        $query = GameAccount::query()->with('seller.sellerProfile', 'game');

        $query->when($tab, function ($q, $tab) {
            match ($tab) {
                'pending' => $q->where('status', ListingStatus::PendingReview),
                'approved' => $q->where('status', ListingStatus::Approved),
                'rejected' => $q->where('status', ListingStatus::Rejected),
                'sold' => $q->where('status', ListingStatus::Sold),
                'all' => null,
                default => $q->where('status', ListingStatus::PendingReview),
            };
        });

        $accounts = $query->latest()->paginate(15);

        return view('admin.accounts', compact('accounts', 'tab'));
    }

    public function approve(GameAccount $gameAccount, Request $request): RedirectResponse
    {
        $gameAccount->update([
            'status' => ListingStatus::Approved,
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        Notifier::notify($gameAccount->seller, 'account', 'Listing disetujui!', "'{$gameAccount->title}' sudah live di marketplace.");

        if ($request->boolean('next')) {
            return redirect()->route('admin.accounts', ['tab' => 'pending']);
        }

        return back()->with('status', 'Listing disetujui dan live.');
    }

    public function reject(GameAccount $gameAccount, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ], [], ['reason' => 'Alasan penolakan']);

        $gameAccount->update([
            'status' => ListingStatus::Rejected,
            'rejection_reason' => $request->string('reason')->trim()->toString(),
        ]);

        Notifier::notify($gameAccount->seller, 'account', 'Listing ditolak', "'{$gameAccount->title}' ditolak. Alasan: {$gameAccount->rejection_reason}");

        return back()->with('status', 'Listing ditolak.');
    }

    public function adminRequests(): View
    {
        $requests = SellerRequest::query()
            ->with('user')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC")
            ->paginate(15);

        return view('admin.seller-requests', compact('requests'));
    }

    public function reviewRequest(SellerRequest $sellerRequest, Request $request): RedirectResponse
    {
        abort_if($sellerRequest->status !== SellerRequestStatus::Pending, 403);

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $sellerRequest->user;

        if ($request->string('action')->toString() === 'approve') {
            $slug = Str::slug($sellerRequest->store_name);

            SellerProfile::query()->createOrFirst([
                'user_id' => $user->id,
                'store_name' => $sellerRequest->store_name,
                'slug' => $this->uniqueStoreSlug($slug),
                'membership_tier' => 'standard',
                'is_official_verified' => false,
                'total_sales' => 0,
                'rating_cache' => 0,
            ]);

            $user->update(['role' => 'seller']);

            $sellerRequest->update([
                'status' => SellerRequestStatus::Approved,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_note' => $request->string('admin_note')->toString() ?: null,
            ]);

            Notifier::notify($user, 'seller', 'Selamat, kamu jadi penjual!', "Toko '{$sellerRequest->store_name}' resmi aktif. Kamu bisa mulai menjual akun.");
        } else {
            $sellerRequest->update([
                'status' => SellerRequestStatus::Rejected,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_note' => $request->string('admin_note')->toString() ?: null,
            ]);

            Notifier::notify($user, 'seller', 'Pengajuan jadi penjual ditolak', $sellerRequest->admin_note ?: 'Mohon tinjau kembali data kamu dan ajukan ulang.');
        }

        return back()->with('status', 'Pengajuan seller diproses.');
    }

    private function authorizeOwnership(GameAccount $gameAccount): void
    {
        abort_if($gameAccount->seller_id !== auth()->id(), 403);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'akun-game';
        $slug = $base;
        $i = 2;

        while (GameAccount::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function uniqueStoreSlug(string $slug): string
    {
        $base = $slug ?: 'toko';
        $candidate = $base;
        $i = 2;

        while (SellerProfile::query()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.$i++;
        }

        return $candidate;
    }

    private function storeImages(GameAccount $account, Request $request): void
    {
        $position = $account->images()->max('position') ?? -1;
        $newCaptions = (array) $request->input('new_captions', []);
        $index = 0;

        foreach ($request->file('images', []) as $file) {
            $path = $file->store('game-accounts/'.$account->getKey(), 'public');
            $caption = $newCaptions[$index] ?? null;

            $account->images()->create([
                'path' => $path,
                'caption' => is_string($caption) && trim($caption) !== '' ? trim($caption) : null,
                'position' => ++$position,
            ]);

            $index++;
        }

        $account->unsetRelation('images');
    }

    private function syncImages(GameAccount $account, Request $request): void
    {
        foreach ((array) $request->input('remove_images', []) as $imageId) {
            $image = $account->images()->whereKey((int) $imageId)->first();

            if (! $image) {
                continue;
            }

            $this->deleteImageFile($image);
            $image->delete();
        }

        $captions = (array) $request->input('captions', []);

        foreach ($captions as $imageId => $caption) {
            $account->images()->whereKey((int) $imageId)->update([
                'caption' => is_string($caption) ? trim($caption) ?: null : null,
            ]);
        }

        $this->storeImages($account, $request);
    }

    private function deleteImageFile(AccountImage $image): void
    {
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }
    }
}
