<?php

namespace Database\Seeders;

use App\Enums\DisputePriority;
use App\Enums\DisputeStatus;
use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Enums\WalletDirection;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use App\Models\AccountImage;
use App\Models\Conversation;
use App\Models\Dispute;
use App\Models\Game;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Wishlist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds with realistic demo data.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@gamevault.test'],
            [
                'name' => 'Admin Rekber',
                'username' => 'admin.rekber',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'is_verified' => true,
            ],
        );
        $this->wallet($admin);

        $arbitrator = User::query()->updateOrCreate(
            ['email' => 'arbitrator@gamevault.test'],
            [
                'name' => 'Naia Ardipa',
                'username' => 'naia.ardipa',
                'password' => bcrypt('password'),
                'role' => UserRole::Arbitrator,
                'is_verified' => true,
            ],
        );
        $this->wallet($arbitrator);

        $buyer = User::query()->updateOrCreate(
            ['email' => 'buyer@gamevault.test'],
            [
                'name' => 'Adit Senjaya',
                'username' => 'adit.senjaya',
                'password' => bcrypt('password'),
                'role' => UserRole::Buyer,
                'is_verified' => true,
            ],
        );
        $this->wallet($buyer, 5_250_000);

        $sellers = [
            [
                'name' => 'Renata Violet',
                'username' => 'renata.violet',
                'email' => 'renata@gamevault.test',
                'store' => 'Renata Official Store',
                'tier' => 'diamond',
                'verified' => true,
                'bio' => 'Seller #1 Mobile Legends - 1200+ transaksi sukses. Garansi anti hackback.',
            ],
            [
                'name' => 'Imam Nugraha',
                'username' => 'imam.nugraha',
                'email' => 'imam@gamevault.test',
                'store' => 'GAME META Berkah Store',
                'tier' => 'gold',
                'verified' => true,
                'bio' => 'Jual akun gacor & murni, proses 5 menit.',
            ],
            [
                'name' => 'Sofie Aurora',
                'username' => 'sofie.aurora',
                'email' => 'sofie@gamevault.test',
                'store' => 'Aurora Reborn Store',
                'tier' => 'silver',
                'verified' => false,
                'bio' => 'Akun-akun berkualitas dengan harga bersahabat.',
            ],
        ];

        $ml = Game::query()->where('slug', 'mobile-legends')->firstOrFail();
        $valorant = Game::query()->where('slug', 'valorant')->firstOrFail();
        $ff = Game::query()->where('slug', 'free-fire')->firstOrFail();
        $genshin = Game::query()->where('slug', 'genshin-impact')->firstOrFail();
        $pubg = Game::query()->where('slug', 'pubg-mobile')->firstOrFail();
        $codm = Game::query()->where('slug', 'call-of-duty-mobile')->firstOrFail();

        $sellerUsers = [];
        foreach ($sellers as $index => $data) {
            $seller = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => bcrypt('password'),
                    'role' => UserRole::Seller,
                    'is_verified' => $data['verified'],
                    'bio' => $data['bio'],
                ],
            );
            $this->wallet($seller, 2_500_000 + $index * 750_000);
            SellerProfile::query()->updateOrCreate(
                ['user_id' => $seller->id],
                [
                    'store_name' => $data['store'],
                    'slug' => str($data['store'])->slug().'-'.($index + 1),
                    'bio' => $data['bio'],
                    'membership_tier' => $data['tier'],
                    'is_official_verified' => $data['verified'],
                    'total_sales' => 1200 + $index * 220,
                    'rating_cache' => 4.8 + $index * 0.05,
                ],
            );
            $sellerUsers[] = $seller;
        }

        Game::query()->where('is_active', true)->get()->each(function (Game $game): void {
            $this->publishGameIcon($game);
        });

        $accountsSpec = [
            [
                'seller' => $sellerUsers[0], 'game' => $ml, 'title' => 'MLBB ID 8912455 - Exp 99, Hero 118',
                'title2' => 'MLBB ID 8912455 - S27 Mythic Glory (4.9K Points)',
                'price' => 6_750_000, 'strike' => 8_500_000, 'rank' => 'Mythic Glory', 'level' => 99,
                'heroes' => 118, 'skins' => 26, 'winrate' => 61.3, 'servers' => 'Asia',
                'badges' => ['Skin Limited Rare', 'Kolektor 3', 'Gokai', 'Campaign Stray'],
                'instant' => true, 'featured' => true,
            ],
            [
                'seller' => $sellerUsers[0], 'game' => $ml, 'title' => 'MLBB - Odette Butterfly Seraphim + 3.600 Dia',
                'price' => 3_250_000, 'strike' => 4_300_000, 'rank' => 'Mythic', 'level' => 72,
                'heroes' => 95, 'skins' => 78, 'winrate' => 55.8, 'servers' => 'Asia',
                'badges' => ['Skin Mewah', 'Diamond 3.6K'],
                'instant' => true, 'featured' => false,
            ],
            [
                'seller' => $sellerUsers[1], 'game' => $valorant, 'title' => 'VALORANT - Radiant 1.200RR Full Bundle yang Paling Diminati',
                'price' => 4_200_000, 'strike' => 5_200_000, 'rank' => 'Radiant', 'level' => 280,
                'heroes' => 18, 'skins' => 142, 'winrate' => 58.2, 'servers' => 'Singapore',
                'badges' => ['Skins Valorant', 'Radio Komputer', 'Vandal Abyss'],
                'instant' => true, 'featured' => true,
            ],
            [
                'seller' => $sellerUsers[1], 'game' => $ff, 'title' => 'FF - M8903 Ace Master Dengan 4.500+ Skin',
                'price' => 2_850_000, 'strike' => 3_600_000, 'rank' => 'Ace Master', 'level' => 96,
                'heroes' => 42, 'skins' => 215, 'winrate' => 49.5, 'servers' => 'Indonesia',
                'badges' => ['Pet Mewah', 'Karakter Lengkap'],
                'instant' => true, 'featured' => false,
            ],
            [
                'seller' => $sellerUsers[2], 'game' => $genshin, 'title' => 'GI AR60 Raiden + Miko + 8 Char 5* 3500+Resin',
                'price' => 5_900_000, 'strike' => 7_200_000, 'rank' => 'AR 60', 'level' => 300,
                'heroes' => 34, 'skins' => 12, 'winrate' => null, 'servers' => 'Asia',
                'badges' => ['Limited', 'Venti + Zhongli', '5* Senjata'],
                'instant' => false, 'featured' => true,
            ],
            [
                'seller' => $sellerUsers[2], 'game' => $pubg, 'title' => 'PUBGM Conqueror 8252 PTS - Full Legends (Rare Suit Reborn)',
                'price' => 3_900_000, 'strike' => 4_600_000, 'rank' => 'Conqueror', 'level' => 120,
                'heroes' => 0, 'skins' => 96, 'winrate' => null, 'servers' => 'Asia',
                'badges' => ['Outfit Rare', 'Scar Sepuh'],
                'instant' => true, 'featured' => false,
            ],
        ];

        foreach ($accountsSpec as $index => $spec) {
            $base = str($spec['title'])->slug().'-'.($index + 100);
            $account = GameAccount::query()->updateOrCreate(
                ['slug' => $base],
                [
                    'seller_id' => $spec['seller']->id,
                    'game_id' => $spec['game']->id,
                    'title' => $spec['title'],
                    'description' => 'Akun '.$spec['game']->name.' premium dengan rank '.$spec['rank'].'. Akun 100% murni, email bisa diganti, terverifikasi rekber GameVault dengan garansi anti hackback 100%.',
                    'price' => $spec['price'],
                    'strike_price' => $spec['strike'],
                    'discount_percent' => (int) round((($spec['strike'] - $spec['price']) / $spec['strike']) * 100),
                    'server' => $spec['servers'],
                    'rank' => $spec['rank'],
                    'level' => $spec['level'],
                    'heros_count' => $spec['heroes'],
                    'skins_count' => $spec['skins'],
                    'winrate' => $spec['winrate'],
                    'rarity_metrics' => [
                        'diamonds' => fake()->numberBetween(500, 8_000),
                        'skins' => $spec['skins'],
                        'events' => fake()->numberBetween(1, 9),
                    ],
                    'features' => collect($spec['badges'])->map(fn (string $label) => [
                        'label' => $label,
                        'prominent' => true,
                        'icon' => 'star',
                    ])->values()->all(),
                    'instant_delivery' => $spec['instant'],
                    'is_featured' => $spec['featured'],
                    'status' => ListingStatus::Approved,
                    'views_count' => fake()->numberBetween(150, 3_200),
                    'published_at' => now()->subDays(fake()->numberBetween(1, 14)),
                    'handover_data' => encrypt(json_encode([
                        'email' => str($spec['title'])->slug().'@gamevault.test',
                        'password' => 'Ga$eVault#2026',
                    ])),
                ],
            );

            $this->attachAccountImages($account, $spec['game']->icon_color, [
                'Rank '.$spec['rank'],
                'Koleksi Skin & Item',
                'Detail Akun Lengkap',
            ]);
        }

        $demoAccount = GameAccount::query()->where('slug', 'like', 'mlbb-id-8912455%')->first();
        Wishlist::query()->updateOrCreate(
            ['user_id' => $buyer->id, 'game_account_id' => $demoAccount->id],
        );

        $soldSpec = GameAccount::query()
            ->where('seller_id', $sellerUsers[1]->id)
            ->first();
        $soldSpec?->update(['status' => ListingStatus::Sold, 'sold_at' => now()->subDays(3)]);

        $order = Order::query()->updateOrCreate(
            ['order_no' => 'GV-EX-001'],
            [
                'buyer_id' => $buyer->id,
                'seller_id' => $sellerUsers[1]->id,
                'game_account_id' => $soldSpec?->id,
                'subtotal' => $soldSpec?->price ?? 3_250_000,
                'discount_amount' => $soldSpec?->strike_price ? ($soldSpec->strike_price - $soldSpec->price) : 0,
                'fee_amount' => 20_000,
                'total_amount' => ($soldSpec?->price ?? 3_250_000) + 20_000 - ($soldSpec?->strike_price ? ($soldSpec->strike_price - $soldSpec->price) : 0),
                'payment_method' => PaymentMethod::Wallet->value,
                'status' => OrderStatus::Completed,
                'buyer_confirmed_at' => now()->subDays(3),
                'completed_at' => now()->subDays(3)->addMinutes(75),
            ],
        );

        Payment::query()->updateOrCreate(
            ['reference' => 'PAY-EX-001'],
            [
                'order_id' => $order->id,
                'method' => PaymentMethod::Wallet->value,
                'amount' => $order->total_amount,
                'fee' => 0,
                'status' => PaymentStatus::Paid,
                'paid_at' => now()->subDays(4),
            ],
        );

        Review::query()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'reviewer_id' => $buyer->id,
                'seller_id' => $sellerUsers[1]->id,
                'game_account_id' => $soldSpec?->id,
                'rating' => 5,
                'content' => 'Prosesnya cepat banget, seller ramah dan akun 100% aman. Recommended! ⭐⭐⭐⭐⭐',
                'verified_escrow' => true,
            ],
        );

        $activeOrder = Order::query()->updateOrCreate(
            ['order_no' => 'GV-EX-002'],
            [
                'buyer_id' => $buyer->id,
                'seller_id' => $sellerUsers[0]->id,
                'game_account_id' => $demoAccount?->id,
                'subtotal' => $demoAccount?->price ?? 5_900_000,
                'discount_amount' => $demoAccount?->strike_price ? ($demoAccount->strike_price - $demoAccount->price) : 0,
                'fee_amount' => 25_000,
                'total_amount' => ($demoAccount?->price ?? 5_900_000) + 25_000 - ($demoAccount?->strike_price ? ($demoAccount->strike_price - $demoAccount->price) : 0),
                'payment_method' => PaymentMethod::Qris->value,
                'status' => OrderStatus::Escrow,
                'escrow_deadline' => now()->addHours(2),
            ],
        );

        Payment::query()->updateOrCreate(
            ['reference' => 'PAY-EX-002'],
            [
                'order_id' => $activeOrder->id,
                'method' => PaymentMethod::Qris->value,
                'amount' => $activeOrder->total_amount,
                'fee' => 0,
                'status' => PaymentStatus::Paid,
                'paid_at' => now()->subHour(),
            ],
        );

        $conversation = Conversation::query()->updateOrCreate(
            ['order_id' => $activeOrder->id, 'buyer_id' => $buyer->id, 'seller_id' => $sellerUsers[0]->id],
            ['last_message_at' => now()->subMinutes(8)],
        );
        $conversation->messages()->create(['sender_id' => $buyer->id, 'body' => 'Cek dulu ya bang, lancar ini.']);
        $conversation->messages()->create(['sender_id' => $sellerUsers[0]->id, 'body' => 'Siap bro, 2 menit lagi aku kirim.']);

        Dispute::query()->updateOrCreate(
            ['case_no' => 'ARB-9001'],
            [
                'order_id' => $order->id,
                'opened_by' => $buyer->id,
                'opponent_id' => $sellerUsers[1]->id,
                'resolved_by' => $arbitrator->id,
                'category' => 'Akun dihack',
                'reason' => 'Akun kena hackback setelah 2 hari transaksi.',
                'status' => DisputeStatus::Resolved,
                'priority' => DisputePriority::High,
                'resolution' => 'seller_win',
                'resolution_note' => 'Bukti kepemilikan email valid dipegang pembeli.',
                'resolved_at' => now()->subDay(),
            ],
        );

        $admin->notifications()->create([
            'type' => 'platform',
            'title' => 'Pencairan baru menunggu review',
            'body' => 'Seller GamingHub Store mengajukan pencairan Rp 5.200.000.',
            'data' => ['type' => 'withdrawal', 'reference' => 'WD-2241'],
        ]);

        $buyer->notifications()->create([
            'type' => 'platform',
            'title' => 'Transaksi berhasil - Order #GV-EX-001',
            'body' => 'Akun telah diterima. Jangan lupa beri rating ya!',
            'data' => ['type' => 'order', 'reference' => 'GV-EX-001'],
        ]);
    }

    private function publishGameIcon(Game $game): void
    {
        $path = 'games/'.$game->slug.'/icon.svg';

        Storage::disk('public')->put($path, $this->iconSvg($game->name, $game->icon_color));

        if ($game->icon_url !== $path) {
            $game->update(['icon_url' => $path]);
        }
    }

    private function iconSvg(string $name, string $color): string
    {
        $label = mb_substr($name, 0, 2);

        return '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">'
            .'<rect width="96" height="96" rx="22" fill="'.e($color).'"/>'
            .'<text x="48" y="62" font-family="Arial, sans-serif" font-size="38" font-weight="700" fill="#131316" text-anchor="middle">'.e($label).'</text>'
            .'</svg>';
    }

    private function attachAccountImages(GameAccount $account, string $accent, array $captions): void
    {
        foreach ($captions as $index => $caption) {
            $path = 'game-accounts/'.$account->getKey().'/shot-'.($index + 1).'.svg';

            Storage::disk('public')->put($path, $this->shotSvg($accent, $index + 1));

            AccountImage::query()->updateOrCreate(
                ['game_account_id' => $account->getKey(), 'position' => $index],
                ['path' => $path, 'caption' => $caption],
            );
        }
    }

    private function shotSvg(string $accent, int $index): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="1000" viewBox="0 0 1600 1000">'
            .'<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            .'<stop offset="0" stop-color="#17171d"/>'
            .'<stop offset="1" stop-color="'.e($accent).'33"/>'
            .'</linearGradient></defs>'
            .'<rect width="1600" height="1000" fill="url(#g)"/>'
            .'<rect x="90" y="80" rx="24" width="430" height="260" fill="#ffffff14"/>'
            .'<rect x="90" y="360" rx="24" width="430" height="300" fill="#ffffff0f"/>'
            .'<rect x="540" y="80" rx="24" width="970" height="580" fill="#ffffff0d"/>'
            .'<text x="1140" y="740" font-family="Arial, sans-serif" font-size="34" font-weight="600" fill="#ffffff55" text-anchor="middle">GameVault Demo Screenshot #'.$index.'</text>'
            .'</svg>';
    }

    private function wallet(User $user, int $balance = 0): Wallet
    {
        $wallet = Wallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['available_balance' => 0, 'escrow_balance' => 0],
        );

        if ($balance > 0 && $wallet->available_balance === 0) {
            $wallet->increment('available_balance', $balance);
            $wallet->refresh();

            $wallet->transactions()->create([
                'type' => WalletTransactionType::Deposit->value,
                'direction' => WalletDirection::In->value,
                'amount' => $balance,
                'balance_after' => $wallet->available_balance + $wallet->escrow_balance,
                'description' => 'Saldo awal demo',
                'status' => WalletTransactionStatus::Success,
            ]);
        }

        return $wallet;
    }
}
