<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'phone', 'bio', 'role', 'is_verified', 'avatar', 'two_factor_enabled', 'two_factor_secret', 'two_factor_confirmed_at', 'two_factor_recovery_codes'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_verified' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function gameAccounts(): HasMany
    {
        return $this->hasMany(GameAccount::class, 'seller_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function sellerRequests(): HasMany
    {
        return $this->hasMany(SellerRequest::class);
    }

    public function payoutAccounts(): HasMany
    {
        return $this->hasMany(PayoutAccount::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function isSeller(): bool
    {
        return $this->role->value === UserRole::Seller->value;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isArbitrator(): bool
    {
        return $this->role === UserRole::Arbitrator;
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isArbitrator();
    }

    public function roleLabel(): string
    {
        if ($this->isArbitrator()) {
            return 'Tim Rekber';
        }

        return $this->role->label();
    }

    /**
     * The initial-based avatar initial used across the UI.
     */
    protected function initials(): Attribute
    {
        return Attribute::get(fn (): string => strtoupper(mb_substr($this->username, 0, 1)) ?: 'U');
    }

    /**
     * Format the account balance without monetary conversion.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(fn () => $this->avatar ? asset($this->avatar) : null);
    }

    public function hasWallet(): bool
    {
        return $this->wallet()->exists();
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }
}
