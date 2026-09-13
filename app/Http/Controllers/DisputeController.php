<?php

namespace App\Http\Controllers;

use App\Enums\DisputePriority;
use App\Enums\DisputeStatus;
use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\WalletDirection;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use App\Models\Dispute;
use App\Models\Order;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DisputeController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $disputes = Dispute::query()
            ->with(['order.gameAccount.game', 'openedBy', 'opponent'])
            ->where(fn ($q) => $q->where('opened_by', $user->id)->orWhere('opponent_id', $user->id))
            ->latest()
            ->paginate(10);

        return view('disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute): View
    {
        $user = auth()->user();

        abort_unless(
            $dispute->opened_by === $user->id
            || $dispute->opponent_id === $user->id
            || $user->isStaff(),
            404
        );

        $dispute->load(['order.gameAccount.game', 'openedBy', 'opponent', 'resolver', 'messages.sender']);

        return view('disputes.show', compact('dispute'));
    }

    public function store(Order $order, Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($order->buyer_id === $user->id || $order->seller_id === $user->id, 403);

        if (! in_array($order->status, [OrderStatus::Escrow, OrderStatus::Handover, OrderStatus::BuyerConfirmation], true)) {
            return back()->with('error', 'Dispute hanya bisa dibuka saat transaksi sedang berjalan.');
        }

        if ($order->dispute()->whereNot('status', DisputeStatus::Resolved)->exists()) {
            return back()->with('error', 'Sudah ada dispute aktif untuk pesanan ini.');
        }

        $request->validate([
            'category' => ['required', 'string', 'in:account_unreceived,account_not_match,credentials_invalid,other'],
            'description' => ['required', 'string', 'max:1000'],
        ], [], ['category' => 'Kategori', 'description' => 'Deskripsi masalah']);

        $categoryLabels = [
            'account_unreceived' => 'Akun tidak diterima',
            'account_not_match' => 'Akun tidak sesuai deskripsi',
            'credentials_invalid' => 'Login/ganti sandi gagal',
            'other' => 'Lainnya',
        ];

        $opponent = $order->buyer_id === $user->id ? $order->seller : $order->buyer;

        $dispute = Dispute::query()->create([
            'case_no' => 'DSP-'.now()->format('ymd').'-'.strtoupper(Str::random(5)),
            'order_id' => $order->id,
            'opened_by' => $user->id,
            'opponent_id' => $opponent->id,
            'category' => $request->string('category')->toString(),
            'reason' => $categoryLabels[$request->string('category')->toString()] ?? 'Lainnya',
            'description' => $request->string('description')->trim()->toString(),
            'status' => DisputeStatus::Open,
            'priority' => DisputePriority::High,
            'evidence' => [],
            'escalated_to_arbitration' => false,
        ]);

        $dispute->messages()->create([
            'sender_id' => $user->id,
            'body' => $request->string('description')->trim()->toString(),
            'role' => 'customer',
        ]);

        $order->forceFill(['status' => OrderStatus::Disputed])->save();

        Notifier::notify($opponent, 'dispute', 'Dispute dibuka', "{$user->username} membuka dispute {$dispute->case_no} pada pesanan #{$order->order_no}. Kasus sudah ditangani tim rekber GameVault.");

        return to_route('disputes.show', $dispute)->with('status', 'Dispute dibuka. Tim Rekber akan segera menangani.');
    }

    public function storeMessage(Dispute $dispute, Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($dispute->opened_by === $user->id || $dispute->opponent_id === $user->id || $user->isStaff(), 403);
        abort_if($dispute->status === DisputeStatus::Resolved, 403);

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $dispute->messages()->create([
            'sender_id' => $user->id,
            'role' => $user->isStaff() ? 'staff' : 'customer',
            'body' => $request->string('body')->trim()->toString(),
        ]);

        if (! $user->isStaff() && $dispute->status === DisputeStatus::Open) {
            $dispute->update(['status' => DisputeStatus::UnderReview]);
        }

        $recipient = match (true) {
            $user->isStaff() => $dispute->opened_by === $user->id ? $dispute->opponent : $dispute->openedBy,
            $user->id === $dispute->opened_by => $dispute->opponent,
            default => $dispute->openedBy,
        };

        Notifier::notify($recipient, 'dispute', 'Pesan dispute baru', "Pesan baru pada kasus {$dispute->case_no}.");

        return back()->with('status', 'Pesan terkirim.');
    }

    public function admin(): View
    {
        $disputes = Dispute::query()
            ->with(['order.gameAccount.game', 'openedBy', 'opponent'])
            ->orderByRaw("FIELD(status, 'open', 'under_review', 'waiting_buyer', 'waiting_seller', 'resolved'), created_at DESC")
            ->paginate(15);

        return view('admin.disputes', compact('disputes'));
    }

    public function resolve(Dispute $dispute, Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isStaff(), 403);
        abort_if($dispute->status === DisputeStatus::Resolved, 403);

        $request->validate([
            'decision' => ['required', 'in:buyer_win,seller_win'],
            'resolution_note' => ['required', 'string', 'max:1000'],
        ], [], ['decision' => 'Keputusan', 'resolution_note' => 'Alasan keputusan']);

        $order = $dispute->order;
        $buyerWins = $request->string('decision')->toString() === 'buyer_win';

        if ($buyerWins) {
            $this->settleForBuyer($order);
        } else {
            $this->settleForSeller($order);
        }

        $dispute->update([
            'status' => DisputeStatus::Resolved,
            'resolved_by' => auth()->id(),
            'resolution' => $buyerWins ? 'buyer_win' : 'seller_win',
            'resolution_note' => $request->string('resolution_note')->trim()->toString(),
            'resolved_at' => now(),
        ]);

        $winner = $buyerWins ? $order->buyer : $order->seller;

        Notifier::notify($winner, 'dispute', 'Dispute selesai', "Keputusan kasus {$dispute->case_no} memenangkan kamu.");
        Notifier::notify($buyerWins ? $order->seller : $order->buyer, 'dispute', 'Dispute selesai', "Keputusan kasus {$dispute->case_no} telah ditetapkan oleh tim rekber.");

        return back()->with('status', 'Keputusan dispute disimpan.');
    }

    private function settleForBuyer(Order $order): void
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
                'description' => "Refund keputusan dispute #{$order->order_no}",
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'status' => WalletTransactionStatus::Success,
            ]);
        }

        $order->forceFill(['status' => OrderStatus::Refunded, 'refunded_at' => now()])->save();
    }

    private function settleForSeller(Order $order): void
    {
        if ($order->payment_method === 'wallet') {
            $buyerWallet = $order->buyer->wallet;
            $buyerWallet->decrement('escrow_balance', $order->total_amount);
            $buyerWallet->refresh();
            $buyerWallet->transactions()->create([
                'type' => WalletTransactionType::EscrowRelease,
                'direction' => WalletDirection::Out,
                'amount' => $order->total_amount,
                'balance_after' => $buyerWallet->available_balance + $buyerWallet->escrow_balance,
                'description' => "Rilis escrow keputusan dispute #{$order->order_no}",
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'status' => WalletTransactionStatus::Success,
            ]);
        }

        $payout = $order->total_amount - $order->fee_amount;

        $sellerWallet = $order->seller->wallet;
        $sellerWallet->increment('available_balance', $payout);
        $sellerWallet->refresh();
        $sellerWallet->transactions()->create([
            'type' => WalletTransactionType::EscrowRelease,
            'direction' => WalletDirection::In,
            'amount' => $payout,
            'balance_after' => $sellerWallet->available_balance + $sellerWallet->escrow_balance,
            'description' => "Hasil penjualan #{$order->order_no} (diputus tim rekber)",
            'reference_type' => Order::class,
            'reference_id' => $order->id,
            'status' => WalletTransactionStatus::Success,
        ]);

        $order->gameAccount->forceFill([
            'status' => ListingStatus::Sold,
            'sold_at' => now(),
        ])->save();

        $order->forceFill(['status' => OrderStatus::Completed, 'completed_at' => now()])->save();
    }
}
