<?php

namespace Database\Factories;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SellerProfile>
 */
class SellerProfileFactory extends Factory
{
    protected $model = SellerProfile::class;

    public function definition(): array
    {
        $storeName = fake()->unique()->company();

        return [
            'user_id' => User::factory()->asSeller(),
            'store_name' => $storeName,
            'slug' => Str::slug($storeName).'-'.fake()->unique()->numberBetween(100, 999),
            'bio' => fake()->paragraph(1),
            'membership_tier' => fake()->randomElement(['silver', 'gold', 'diamond']),
            'is_official_verified' => fake()->boolean(50),
            'total_sales' => fake()->numberBetween(20, 4_000),
            'rating_cache' => fake()->randomFloat(1, 4.0, 5.0),
            'response_time_minutes' => fake()->randomElement([1, 2, 5, 10, 30]),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_official_verified' => true,
            'membership_tier' => 'diamond',
        ]);
    }
}
