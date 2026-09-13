<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Sold = 'sold';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingReview => 'Menunggu Review',
            self::Approved => 'Disetujui / Live',
            self::Rejected => 'Ditolak',
            self::Sold => 'Terjual',
            self::Suspended => 'Disuspend',
        };
    }
}
