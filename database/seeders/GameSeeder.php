<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'icon_color' => '#4cd7f6', 'sort_order' => 1],
            ['name' => 'Valorant', 'slug' => 'valorant', 'icon_color' => '#ffb4ab', 'sort_order' => 2],
            ['name' => 'Free Fire', 'slug' => 'free-fire', 'icon_color' => '#f2b8b5', 'sort_order' => 3],
            ['name' => 'Roblox', 'slug' => 'roblox', 'icon_color' => '#8a93ff', 'sort_order' => 4],
            ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'icon_color' => '#ffd59f', 'sort_order' => 5],
            ['name' => 'Genshin Impact', 'slug' => 'genshin-impact', 'icon_color' => '#4edea3', 'sort_order' => 6],
            ['name' => 'Call of Duty Mobile', 'slug' => 'call-of-duty-mobile', 'icon_color' => '#c58af9', 'sort_order' => 7],
            ['name' => 'EA FC Mobile', 'slug' => 'ea-fc-mobile', 'icon_color' => '#6ee7b7', 'sort_order' => 8],
            ['name' => 'Steam / CS2', 'slug' => 'steam-cs2', 'icon_color' => '#d0bcff', 'sort_order' => 9],
            ['name' => 'Honor of Kings', 'slug' => 'honor-of-kings', 'icon_color' => '#f9bec7', 'sort_order' => 10],
            ['name' => 'Arena Breakout', 'slug' => 'arena-breakout', 'icon_color' => '#9ad0ff', 'sort_order' => 11],
        ];

        foreach ($games as $game) {
            Game::query()->updateOrCreate(
                ['slug' => $game['slug']],
                [...$game, 'is_active' => true],
            );
        }
    }
}
