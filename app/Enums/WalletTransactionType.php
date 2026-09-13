<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case EscrowHold = 'escrow_hold';
    case EscrowRelease = 'escrow_release';
    case EscrowRefund = 'escrow_refund';
    case Adjustment = 'adjustment';
}
