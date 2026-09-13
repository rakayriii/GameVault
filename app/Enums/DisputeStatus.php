<?php

namespace App\Enums;

enum DisputeStatus: string
{
    case Open = 'open';
    case UnderReview = 'under_review';
    case WaitingBuyer = 'waiting_buyer';
    case WaitingSeller = 'waiting_seller';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Baru Dibuka',
            self::UnderReview => 'Diproses Arbitrator',
            self::WaitingBuyer => 'Menunggu Buyer',
            self::WaitingSeller => 'Menunggu Seller',
            self::Resolved => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }
}
