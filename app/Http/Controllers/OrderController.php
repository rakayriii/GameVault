<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use App\Enums\OrderStatus;
use App\Enums\WalletDirection;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\Review;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->with(['gameAccount.game', 'seller.sellerProfile'])
            ->where('buyer_id', auth()->id());

        if ($request->filled('status') && in_array($request->string('status')->toString(), array_column(OrderStatus::cases(), 'value'), true)) {
            $query->where('status', $request->string('status')->toString());
        } else {
            $request->request->set('status', '');
        }

        $orders = $query->latest()->paginate(10);

        return view('orders.index', ['orders' => $orders, 'status' => $request->string('status')->toString()]);
    }

    public function adminIndex(Request $request): View
    {
        $status = $request->string('status')->toString();

        if ($status !== '' && in_array($status, array_column(OrderStatus::cases(), 'value'), true)) {
            $query = Order::query()->where('status', $status);
        } else {
            $status = '';
            $query = Order::query();
        }

        $query->with(['buyer', 'seller', 'gameAccount.game', 'payments', 'items']);

        if ($request->filled('q')) {
            $q = $request->string('q')->trim()->toString();

            $query->where(function ($sub) use ($q) {
                $sub->where('order_no', 'like', "%{$q}%")
                    ->orWhereHas('buyer', fn ($buyer) => $buyer->where('username', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
                    ->orWhereHas('seller', fn ($seller) => $seller->where('username', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
                    ->orWhereHas('gameAccount', fn ($account) => $account->where('title', 'like', "%{$q}%"));
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders', compact('orders', 'status'));
    }

    public function show(Order $order): View
    {
        $user = auth()->user();

        abort_unless($order->buyer_id === $user->id || $order->seller_id === $user->id || $user->isStaff(), 404);

        $order->load(['gameAccount.game', 'gameAccount.images', 'buyer', 'seller.sellerProfile', 'review']);

        return view('orders.show', compact('order'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 403);

        if (! in_array($order->status, [OrderStatus::PendingPayment, OrderStatus::Escrow], true)) {
            return back()->with('error', 'Pesanan sudah di tahap ini tidak bisa dibatalkan.');
        }

        if ($order->status === OrderStatus::Escrow) {
            $this->refundBuyer($order);
            $order->refunded_at = now();
        }

        $order->forceFill(['status' => OrderStatus::Cancelled, 'cancelled_at' => now()])->save();

        Notifier::notify($order->seller, 'order', 'Pesanan dibatalkan', "Pesanan #{$order->order_no} dibatalkan oleh buyer.");

        return to_route('orders.show', $order)->with('status', 'Pesanan dibatalkan.');
    }

    public function handover(Order $order): View
    {
        $user = auth()->user();

        abort_unless($order->buyer_id === $user->id || $order->seller_id === $user->id || $user->isStaff(), 404);

        if ($order->status === OrderStatus::PendingPayment) {
            abort_unless($user->isStaff(), 404);
        }

        $order->load(['gameAccount.game', 'buyer', 'seller.sellerProfile']);

        $conversation = Conversation::query()
            ->where('order_id', $order->id)
            ->first();

        $messages = $conversation?->messages()->with('sender')->get() ?? collect();

        return view('orders.handover', compact('order', 'conversation', 'messages'));
    }

    public function sendMessage(Order $order, Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($order->buyer_id === $user->id || $order->seller_id === $user->id || $user->isStaff(), 403);

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = Conversation::query()->firstOrCreate([
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
        ]);

        $isSeller = $order->seller_id === $user->id;
        $isCredential = $request->boolean('credential') && $isSeller;

        $type = $isCredential ? MessageType::Credential : MessageType::Text;

        $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $request->string('body')->trim()->toString(),
            'type' => $type,
        ]);

        $conversation->touch();

        if ($isCredential && in_array($order->status, [OrderStatus::Escrow], true)) {
            $order->forceFill([
                'status' => OrderStatus::Handover,
                'handover_deadline' => now()->addHours(2),
            ])->save();

            Notifier::notify($order->buyer, 'handover', 'Kredensial dikirim!', "Seller telah menyerahkan login akun untuk pesanan #{$order->order_no}. Segera verifikasi akun, lalu konfirmasi penerimaan di ruang handover.");
        }

        $opponent = $isSeller ? $order->buyer : $order->seller;
        Notifier::notify($opponent, 'message', 'Pesan baru', "{$user->username} mengirim pesan pada pesanan #{$order->order_no}.");

        return back()->with('status', $isCredential ? 'Kredensial terkirim ke buyer.' : 'Pesan terkirim.');
    }

    public function review(Order $order, Request $request): RedirectResponse
    {
        abort_unless($order->buyer_id === auth()->id(), 403);
        abort_if($order->status !== OrderStatus::Completed, 403);

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['nullable', 'string', 'max:500'],
        ]);

        if ($order->review()->exists()) {
            return back()->with('error', 'Pesanan ini sudah diulas.');
        }

        Review::query()->create([
            'order_id' => $order->id,
            'reviewer_id' => auth()->id(),
            'seller_id' => $order->seller_id,
            'game_account_id' => $order->game_account_id,
            'rating' => $request->integer('rating'),
            'content' => $request->string('content')->trim()->toString(),
            'verified_escrow' => true,
        ]);

        $averagage = Review::query()->where('seller_id', $order->seller_id)->avg('rating');

        $order->seller->sellerProfile?->update(['rating_cache' => round((float) $averagage, 1)]);

        Notifier::notify($order->seller, 'review', 'Ulasan baru!', "Buyer memberi {$request->integer('rating')}★ untuk pesanan #{$order->order_no}.");

        return back()->with('status', 'Terima kasih! Ulasan kamu sudah dipublikasikan.');
    }

    private function refundBuyer(Order $order): void
    {
        if ($order->payment_method === 'wallet') {
            $buyerWallet = $order->buyer->wallet;
            $buyerWallet->decrement('escrow_balance', $order->total_amount);
            $buyerWallet->increment('available_balance', $order->total_amount);
            $buyerWallet->refresh();
            $buyerWallet->transactions()->create([
                'type' => WalletTransactionType::EscrowRefund,
                'direction' => WalletDirection::In,
                'amount' => $order->total_amount,
                'balance_after' => $buyerWallet->available_balance + $buyerWallet->escrow_balance,
                'description' => "Refund pembatalan #{$order->order_no}",
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'status' => WalletTransactionStatus::Success,
            ]);
        }
    }
}
