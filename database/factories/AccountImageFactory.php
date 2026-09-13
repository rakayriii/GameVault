<?php

namespace Database\Factories;

use App\Models\AccountImage;
use App\Models\GameAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountImage>
 */
class AccountImageFactory extends Factory
{
    protected $model = AccountImage::class;

    public function definition(): array
    {
        return [
            'game_account_id' => GameAccount::factory(),
            'path' => 'game-accounts/'.fake()->uuid().'.jpg',
            'caption' => fake()->randomElement([null, 'Lobby', 'Skins', 'Inventory', 'Rank']),
            'position' => fake()->numberBetween(0, 10),
        ];
    }
}
