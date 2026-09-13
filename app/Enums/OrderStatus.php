<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Escrow = 'escrow';
    case Handover = 'handover';
    case BuyerConfirmation = 'buyer_confirmation';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Disputed = 'disputed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::Paid => 'Dibayar',
            self::Escrow => 'Escrow Aktif',
            self::Handover => 'Handover / Live Transfer',
            self::BuyerConfirmation => 'Menunggu Konfirmasi',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
            self::Disputed => 'Dispute / Sengketa',
            self::Refunded => 'Refunded',
        };
    }
}
