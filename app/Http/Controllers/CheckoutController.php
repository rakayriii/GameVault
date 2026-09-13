<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\WalletDirection;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Wallet;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(GameAccount $gameAccount): View|RedirectResponse
    {
        abort_unless($gameAccount->isApproved(), 404);
        abort_if($gameAccount->seller_id === auth()->id(), 403);

        $wallet = auth()->user()->wallet;

        return view('checkout.create', compact('gameAccount', 'wallet'));
    }

    public function store(GameAccount $gameAccount, Request $request): RedirectResponse
    {
        abort_unless($gameAccount->isApproved(), 404);
        abort_if($gameAccount->seller_id === auth()->id(), 403);

        $methods = PaymentMethod::cases();

        $request->validate([
            'payment_method' => ['required', 'string', 'in:'.implode(',', array_column($methods, 'value'))],
            'agree_escrow' => ['required', 'accepted'],
        ], [
            'agree_escrow.required' => 'Kamu harus menyetujui protokol escrow GameVault.',
        ]);

        $method = PaymentMethod::from($request->string('payment_method')->toString());
        $price = $gameAccount->price;
        $strike = $gameAccount->strike_price;
        $fee = $method === PaymentMethod::Wallet ? 0 : min((int) round($price * 0.05), 1_000_000);

        $order = Order::query()->create([
            'order_no' => 'GV-'.now()->format('ymdHis').'-'.strtoupper(Str::random(4)),
            'buyer_id' => auth()->id(),
            'seller_id' => $gameAccount->seller_id,
            'game_account_id' => $gameAccount->id,
            'subtotal' => $strike ?: $price,
            'discount_amount' => $strike ? max($strike - $price, 0) : 0,
            'fee_amount' => $fee,
            'total_amount' => $price + $fee,
            'payment_method' => $method->value,
            'status' => OrderStatus::PendingPayment,
            'payment_due_at' => now()->addMinutes(15),
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'game_account_id' => $gameAccount->id,
            'account_title' => $gameAccount->title,
            'price' => $price,
        ]);

        Notifier::notify($gameAccount->seller, 'order', 'Ada pesanan baru!', "Pesanan #{$order->order_no} untuk '{$gameAccount->title}' menunggu pembayaran dari {$order->buyer->username}.");

        return redirect()->route('checkout.payment', $order)->with('status', 'Pesanan dibuat. Selesaikan pembayaran sebelum batas waktu.');
    }

    public function payment(Order $order): View|RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 404);
        abort_if($order->status !== OrderStatus::PendingPayment, 404);

        return view('checkout.payment', compact('order'));
    }

    public function markPaid(Order $order, Request $request): RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 404);
        abort_if($order->status !== OrderStatus::PendingPayment, 403);

        $method = PaymentMethod::from($order->payment_method);

        if ($method === PaymentMethod::Wallet) {
            /** @var Wallet $wallet */
            $wallet = auth()->user()->wallet;

            if (! $wallet->hasEnoughBalance($order->total_amount)) {
                throw ValidationException::withMessages([
                    'balance' => 'Saldo Rekber tidak cukup. Tambah saldo terlebih dahulu.',
                ])->errorBag('checkout');
            }

            $wallet->decrement('available_balance', $order->total_amount);
            $wallet->increment('escrow_balance', $order->total_amount);
            $wallet->refresh();

            $wallet->transactions()->create([
                'type' => WalletTransactionType::EscrowHold,
                'direction' => WalletDirection::Out,
                'amount' => $order->total_amount,
                'balance_after' => $wallet->available_balance + $wallet->escrow_balance,
                'description' => "Dana tahan escrow untuk #{$order->order_no}",
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'status' => WalletTransactionStatus::Success,
            ]);
        }

        Payment::query()->create([
            'order_id' => $order->id,
            'method' => $method->value,
            'gateway' => 'mock',
            'amount' => $order->total_amount,
            'fee' => $order->fee_amount,
            'reference' => 'MOCK-'.strtoupper(Str::random(10)),
            'status' => PaymentStatus::Paid,
            'payload' => ['channel' => $method->label(), 'simulated' => true],
            'paid_at' => now(),
        ]);

        $order->forceFill([
            'status' => OrderStatus::Escrow,
            'escrow_deadline' => now()->addHours(24),
        ])->save();

        Notifier::notify($order->seller, 'payment', 'Pembayaran terverifikasi!', "Pesanan #{$order->order_no} sudah dibayar. Segera buka ruang handover untuk menyerahkan akun.");

        return redirect()->route('orders.handover', $order)->with('status', 'Pembayaran diterima. Dana masuk Escrow Vault — mulailah handover akun.');
    }

    public function confirm(Order $order, Request $request): RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 404);

        if (! in_array($order->status, [OrderStatus::Escrow, OrderStatus::Handover], true)) {
            return back()->with('error', 'Pesanan belum dalam tahap handover.');
        }

        $request->validate([
            'confirm_check' => ['required', 'accepted'],
        ], ['confirm_check.required' => 'Tandai checklist bahwa kamu sudah memverifikasi akun dan mengganti email / password.']);

        $this->releaseEscrow($order);

        $order->forceFill([
            'status' => OrderStatus::Completed,
            'buyer_confirmed_at' => now(),
            'completed_at' => now(),
        ])->save();

        $order->gameAccount->forceFill([
            'status' => ListingStatus::Sold,
            'sold_at' => now(),
        ])->save();

        Notifier::notify($order->seller, 'order', 'Pesanan selesai & dana dirilis', "Transaksi #{$order->order_no} dikonfirmasi buyer. Dana escrow telah dirilis ke saldo kamu.");

        return redirect()->route('orders.show', $order)->with('status', 'Transaksi selesai! Dana telah dirilis ke seller. Jangan lupa beri ulasan.');
    }

    private function releaseEscrow(Order $order): void
    {
        if ($order->payment_method === PaymentMethod::Wallet->value) {
            $buyerWallet = $order->buyer->wallet;
            $buyerWallet->decrement('escrow_balance', $order->total_amount);
            $buyerWallet->refresh();
            $buyerWallet->transactions()->create([
                'type' => WalletTransactionType::EscrowRelease,
                'direction' => WalletDirection::Out,
                'amount' => $order->total_amount,
                'balance_after' => $buyerWallet->available_balance + $buyerWallet->escrow_balance,
                'description' => "Rilis escrow selesai #{$order->order_no}",
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'status' => WalletTransactionStatus::Success,
            ]);
        }

        $payout = $order->total_amount - $order->fee_amount;

        /** @var Wallet $sellerWallet */
        $sellerWallet = $order->seller->wallet;
        $sellerWallet->increment('available_balance', $payout);
        $sellerWallet->refresh();
        $sellerWallet->transactions()->create([
            'type' => WalletTransactionType::EscrowRelease,
            'direction' => WalletDirection::In,
            'amount' => $payout,
            'balance_after' => $sellerWallet->available_balance + $sellerWallet->escrow_balance,
            'description' => "Hasil penjualan #{$order->order_no}",
            'reference_type' => Order::class,
            'reference_id' => $order->id,
            'status' => WalletTransactionStatus::Success,
        ]);
    }
}
