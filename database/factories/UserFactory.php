<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'username' => Str::slug($name, '.').fake()->unique()->numberBetween(10, 999),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+62'.fake()->numerify('8##########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Buyer,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function asBuyer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => UserRole::Buyer]);
    }

    public function asSeller(): static
    {
        return $this->state(fn (array $attributes) => ['role' => UserRole::Seller]);
    }

    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => UserRole::Admin]);
    }

    public function asArbitrator(): static
    {
        return $this->state(fn (array $attributes) => ['role' => UserRole::Arbitrator]);
    }
}
