<?php

namespace App\Enums;

enum UserRole: string
{
    case Buyer = 'buyer';
    case Seller = 'seller';
    case Arbitrator = 'arbitrator';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Buyer => 'Buyer',
            self::Seller => 'Seller',
            self::Arbitrator => 'Arbitrator',
            self::Admin => 'Admin',
        };
    }
}
