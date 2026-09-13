<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Qris = 'qris';
    case BankVa = 'bank_va';
    case Ewallet = 'ewallet';
    case Wallet = 'wallet';
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Qris => 'QRIS Realtime',
            self::BankVa => 'Virtual Account',
            self::Ewallet => 'Dompet Digital',
            self::Wallet => 'Saldo Rekber',
            self::BankTransfer => 'Transfer Bank',
        };
    }
}
