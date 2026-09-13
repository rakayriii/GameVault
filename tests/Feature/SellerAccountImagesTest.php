<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\AccountImage;
use App\Models\Game;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellerAccountImagesTest extends TestCase
{
    use RefreshDatabase;

    private function makeSellerAndGame(): array
    {
        User::factory()->asAdmin()->create();
        $seller = User::factory()->asSeller()->create();
        SellerProfile::factory()->create(['user_id' => $seller->id]);
        $game = Game::factory()->create();

        return [$seller, $game];
    }

    public function test_seller_can_upload_screenshots_when_creating_a_listing(): void
    {
        Storage::fake('public');
        [$seller, $game] = $this->makeSellerAndGame();

        $payload = [
            'game_id' => $game->id,
            'title' => 'MLBB ID 1234 - Mythic Glory',
            'description' => 'Akun murni tanpa sangkutan.',
            'price' => 1_000_000,
            'images' => [
                $this->fakeImage('lobby.png'),
                $this->fakeImage('skins.png'),
            ],
        ];

        $this->actingAs($seller)
            ->post(route('seller.accounts.store'), $payload)
            ->assertRedirect(route('seller.accounts'));

        $account = $seller->gameAccounts()->firstOrFail();

        $this->assertCount(2, $account->images);
        $this->assertDatabaseCount('account_images', 2);

        $account->images->each(function (AccountImage $image) {
            Storage::disk('public')->assertExists($image->path);
            $this->assertNotNull($image->url());
        });
    }

    public function test_seller_can_edit_a_live_listing_without_losing_approved_status(): void
    {
        [$seller] = $this->makeSellerAndGame();
        $account = GameAccount::factory()->approved()->create(['seller_id' => $seller->id]);

        $this->actingAs($seller)->get(route('seller.accounts.edit', $account))->assertOk();

        $this->actingAs($seller)
            ->put(route('seller.accounts.update', $account), [
                'game_id' => $account->game_id,
                'title' => 'Judul Baru Tetap Live',
                'description' => 'Deskripsi diperbarui.',
                'price' => 2_500_000,
                'strike_price' => null,
            ])
            ->assertRedirect(route('seller.accounts'));

        $account->refresh();

        $this->assertSame('Judul Baru Tetap Live', $account->title);
        $this->assertSame(2_500_000, $account->price);
        $this->assertSame('approved', $account->status->value);
        $this->assertNotNull($account->published_at);
    }

    public function test_seller_can_add_and_remove_images_when_updating_a_listing(): void
    {
        Storage::fake('public');
        [$seller] = $this->makeSellerAndGame();
        $account = GameAccount::factory()->approved()->create(['seller_id' => $seller->id]);

        $old = AccountImage::factory()->create([
            'game_account_id' => $account->id,
            'path' => 'game-accounts/'.$account->id.'/old-shot.png',
        ]);

        $this->actingAs($seller)
            ->put(route('seller.accounts.update', $account), [
                'game_id' => $account->game_id,
                'title' => $account->title,
                'description' => $account->description,
                'price' => $account->price,
                'remove_images' => [$old->id],
                'captions' => [],
                'images' => [
                    $this->fakeImage('new-shot.png'),
                ],
                'new_captions' => ['Lobby utama'],
            ])
            ->assertRedirect(route('seller.accounts'));

        Storage::disk('public')->assertMissing('game-accounts/'.$account->id.'/old-shot.png');
        $this->assertDatabaseMissing('account_images', ['id' => $old->id]);

        $account->refresh();
        $this->assertCount(1, $account->images);
        Storage::disk('public')->assertExists($account->images->first()->path);
        $this->assertSame('Lobby utama', $account->images->first()->caption);
    }

    public function test_seller_cannot_delete_images_of_another_seller(): void
    {
        [$sellerA] = $this->makeSellerAndGame();
        [$sellerB] = $this->makeSellerAndGame();
        $account = GameAccount::factory()->approved()->create(['seller_id' => $sellerA->id]);
        $image = AccountImage::factory()->create(['game_account_id' => $account->id]);

        $this->actingAs($sellerB)
            ->delete(route('seller.accounts.images.destroy', $image))
            ->assertForbidden();

        $this->assertDatabaseHas('account_images', ['id' => $image->id]);
    }

    public function test_seller_dashboard_shows_actionable_order_count(): void
    {
        [$seller] = $this->makeSellerAndGame();
        $buyer = User::factory()->asBuyer()->create();
        $account = GameAccount::factory()->approved()->create(['seller_id' => $seller->id]);

        Order::query()->create([
            'order_no' => 'GV-ESCROW-TEST',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'game_account_id' => $account->id,
            'subtotal' => 500_000,
            'discount_amount' => 0,
            'fee_amount' => 25_000,
            'total_amount' => 525_000,
            'payment_method' => PaymentMethod::Wallet->value,
            'status' => OrderStatus::Escrow,
        ]);

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('1 siap handover')
            ->assertSee($account->title);
    }

    public function test_buyer_can_see_account_images_and_game_logo_on_marketplace(): void
    {
        Storage::fake('public');
        [$seller, $game] = $this->makeSellerAndGame();
        $game->forceFill([
            'icon_url' => 'games/'.$game->slug.'/icon.png',
        ])->save();
        Storage::disk('public')->put('games/'.$game->slug.'/icon.png', 'logo');

        $account = GameAccount::factory()->approved()->for($game)->create(['seller_id' => $seller->id]);

        $image = AccountImage::factory()->create([
            'game_account_id' => $account->id,
            'path' => 'game-accounts/'.$account->id.'/shot.png',
            'caption' => 'Lobby',
            'position' => 0,
        ]);
        Storage::disk('public')->put('game-accounts/'.$account->id.'/shot.png', 'photo');

        $this->get(route('marketplace.show', $account))
            ->assertOk()
            ->assertSee('game-accounts')
            ->assertSee('shot.png')
            ->assertSee('games/'.$game->slug.'/icon.png')
            ->assertSee('Lobby');
    }
}
