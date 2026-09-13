<?php

namespace Database\Factories;

use App\Enums\ListingStatus;
use App\Models\Game;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GameAccount>
 */
class GameAccountFactory extends Factory
{
    protected $model = GameAccount::class;

    public function definition(): array
    {
        $title = fake()->lexify('Akun ');

        return [
            'game_id' => Game::factory(),
            'seller_id' => User::factory()->asSeller(),
            'title' => $title,
            'slug' => Str::slug($title.'-'.fake()->unique()->numberBetween(1000, 9999)),
            'description' => fake()->paragraph(2),
            'price' => fake()->numberBetween(250_000, 15_000_000),
            'strike_price' => fake()->boolean(40) ? fake()->numberBetween(300_000, 18_000_000) : null,
            'server' => fake()->randomElement(['Asia', 'Singapore', 'Jakarta']),
            'region' => fake()->randomElement(['Indonesia', 'Singapore', 'Global']),
            'rank' => fake()->randomElement(['Mythic Glory', 'Immortal', 'Radiant', 'Ace', 'Grandmaster']),
            'rank_tier' => fake()->randomElement(['Glory', 'IV', '25', 'Master']),
            'level' => fake()->numberBetween(10, 120),
            'heros_count' => fake()->numberBetween(20, 120),
            'skins_count' => fake()->numberBetween(15, 240),
            'winrate' => fake()->randomFloat(1, 45, 85),
            'in_game_balance' => fake()->numberBetween(500, 90_000),
            'rarity_metrics' => [
                'diamonds' => fake()->numberBetween(200, 80_000),
                'skins' => fake()->numberBetween(1, 40),
                'events' => fake()->numberBetween(0, 12),
            ],
            'features' => [
                ['label' => 'Skins Limited', 'prominent' => fake()->boolean(70), 'icon' => 'star'],
                ['label' => 'Rekor Tier Tinggi', 'prominent' => fake()->boolean(50), 'icon' => 'military_tech'],
                ['label' => 'Akun Murni', 'prominent' => fake()->boolean(60), 'icon' => 'verified'],
            ],
            'instant_delivery' => fake()->boolean(60),
            'is_featured' => fake()->boolean(15),
            'status' => ListingStatus::Approved,
            'views_count' => fake()->numberBetween(10, 5_000),
            'published_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingStatus::Approved,
            'published_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ListingStatus::PendingReview]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingStatus::Sold,
            'published_at' => now()->subDays(5),
            'sold_at' => now(),
        ]);
    }
}
