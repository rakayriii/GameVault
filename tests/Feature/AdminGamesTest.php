<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGamesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_game_with_icon_and_banner(): void
    {
        Storage::fake('public');
        $admin = User::factory()->asAdmin()->create();

        $this->actingAs($admin)
            ->post(route('admin.games.store'), [
                'name' => 'Mobile Legends',
                'icon_color' => '#ff6b00',
                'description' => 'MOBA 5v5',
                'sort_order' => 1,
                'is_active' => '1',
                'icon' => $this->fakeImage('ml-icon.png'),
                'banner' => $this->fakeImage('ml-banner.png'),
            ])
            ->assertRedirect(route('admin.games'));

        $game = Game::query()->where('slug', 'mobile-legends')->firstOrFail();

        $this->assertTrue($game->is_active);
        Storage::disk('public')->assertExists($game->icon_url);
        Storage::disk('public')->assertExists($game->banner_url);
        $this->assertNotNull($game->iconUrl());
    }

    public function test_admin_can_edit_a_game_and_replace_its_icon(): void
    {
        Storage::fake('public');
        $admin = User::factory()->asAdmin()->create();
        $game = Game::factory()->create(['slug' => 'valorant', 'name' => 'Valorant']);

        $this->actingAs($admin)
            ->put(route('admin.games.update', $game), [
                'name' => 'Valorant (Riot)',
                'icon_color' => '#ff4655',
                'sort_order' => 2,
                'is_active' => '1',
                'icon' => $this->fakeImage('vava-icon.png'),
            ])
            ->assertRedirect(route('admin.games'));

        $game->refresh();

        $this->assertSame('Valorant (Riot)', $game->name);
        Storage::disk('public')->assertExists($game->icon_url);
    }

    public function test_non_staff_cannot_access_games_management(): void
    {
        $seller = User::factory()->asSeller()->create();

        $this->actingAs($seller)->get(route('admin.games'))->assertForbidden();
        $this->actingAs($seller)->get(route('admin.games.create'))->assertForbidden();
    }

    public function test_game_with_listings_cannot_be_deleted(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $game = Game::factory()->create();
        GameAccount::factory()->approved()->for($game)->create();

        $this->actingAs($admin)->delete(route('admin.games.destroy', $game))->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseHas('games', ['id' => $game->id]);
    }

    public function test_game_without_listings_can_be_deleted(): void
    {
        Storage::fake('public');
        $admin = User::factory()->asAdmin()->create();
        $game = Game::factory()->create(['slug' => 'empty-game', 'icon_url' => 'games/empty-game/icon.png']);
        Storage::disk('public')->put('games/empty-game/icon.png', 'logo');

        $this->actingAs($admin)->delete(route('admin.games.destroy', $game))->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseMissing('games', ['id' => $game->id]);
        Storage::disk('public')->assertMissing('games/empty-game/icon.png');
    }

    public function test_admin_can_toggle_game_active_state(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $game = Game::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->post(route('admin.games.toggle', $game))->assertRedirect()->assertSessionHas('status');

        $this->assertFalse($game->refresh()->is_active);

        $this->actingAs($admin)->post(route('admin.games.toggle', $game))->assertRedirect();

        $this->assertTrue($game->refresh()->is_active);
    }

    public function test_admin_can_remove_game_icon_without_replacing(): void
    {
        Storage::fake('public');
        $admin = User::factory()->asAdmin()->create();
        $game = Game::factory()->create([
            'slug' => 'frost-mobile',
            'icon_url' => 'games/frost-mobile/icon.png',
            'banner_url' => 'games/frost-mobile/banner.png',
        ]);
        Storage::disk('public')->put('games/frost-mobile/icon.png', 'logo');
        Storage::disk('public')->put('games/frost-mobile/banner.png', 'banner');

        $this->actingAs($admin)
            ->put(route('admin.games.update', $game), [
                'name' => 'Frost Mobile',
                'icon_color' => '#ffffff',
                'sort_order' => 0,
                'is_active' => '1',
                'remove_icon' => '1',
            ])
            ->assertRedirect(route('admin.games'));

        $game->refresh();

        $this->assertNull($game->icon_url);
        $this->assertNotNull($game->banner_url);
        Storage::disk('public')->assertMissing('games/frost-mobile/icon.png');
        Storage::disk('public')->assertExists('games/frost-mobile/banner.png');
    }
}
