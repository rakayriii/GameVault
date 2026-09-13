<?php

namespace App\Http\Controllers;

use App\Enums\WalletDirection;
use App\Enums\WalletTransactionStatus;
use App\Enums\WalletTransactionType;
use App\Enums\WithdrawalStatus;
use App\Models\PayoutAccount;
use App\Models\Withdrawal;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function index(): View
    {
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        $transactions = $wallet->transactions()->paginate(15);
        $payoutAccounts = auth()->user()->payoutAccounts;

        return view('wallet.index', compact('wallet', 'transactions', 'payoutAccounts'));
    }

    public function deposit(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:100000000'],
            'method' => ['required', 'string', 'in:qris,bank_va,ewallet,bank_transfer'],
        ], [], ['amount' => 'Nominal']);

        $amount = $request->integer('amount');
        $wallet = auth()->user()->wallet()->firstOrCreate([]);
        $wallet->increment('available_balance', $amount);
        $wallet->refresh();

        $wallet->transactions()->create([
            'type' => WalletTransactionType::Deposit,
            'direction' => WalletDirection::In,
            'amount' => $amount,
            'balance_after' => $wallet->available_balance + $wallet->escrow_balance,
            'description' => 'Isi saldo via '.strtoupper($request->string('method')->toString()),
            'status' => WalletTransactionStatus::Success,
        ]);

        return back()->with('status', 'Saldo berhasil ditambahkan.');
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'amount' => ['required', 'integer', 'min:50000', 'max:100000000'],
            'payout_account_id' => ['required', 'exists:payout_accounts,id'],
        ], [], ['amount' => 'Nominal', 'payout_account_id' => 'Rekening tujuan']);

        $payoutAccount = PayoutAccount::query()
            ->where('id', $request->integer('payout_account_id'))
            ->where('user_id', $user->id)
            ->firstOrFail();

        $amount = $request->integer('amount');
        $fee = min((int) round($amount * 0.01), 25000);
        $total = $amount + $fee;

        $wallet = $user->wallet()->firstOrCreate([]);

        if (! $wallet->hasEnoughBalance($total)) {
            return back()->with('error', 'Saldo tidak mencukupi untuk penarikan ini.');
        }

        if (! $user->two_factor_enabled) {
            return back()->with('error', 'Aktifkan autentikasi dua faktor (2FA) sebelum melakukan penarikan dana.');
        }

        $wallet->decrement('available_balance', $total);
        $wallet->refresh();

        $transaction = $wallet->transactions()->create([
            'type' => WalletTransactionType::Withdrawal,
            'direction' => WalletDirection::Out,
            'amount' => $total,
            'balance_after' => $wallet->available_balance + $wallet->escrow_balance,
            'description' => 'Penarikan ke '.$payoutAccount->bank_name.' •••• '.substr($payoutAccount->account_number, -4),
            'status' => WalletTransactionStatus::Pending,
        ]);

        Withdrawal::query()->create([
            'user_id' => $user->id,
            'payout_account_id' => $payoutAccount->id,
            'wallet_transaction_id' => $transaction->id,
            'amount' => $amount,
            'fee' => $fee,
            'status' => WithdrawalStatus::Pending,
            'reference' => 'WD-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),
        ]);

        return back()->with('status', 'Permintaan penarikan dikirim dan menunggu diproses admin.');
    }

    public function withdrawals(): View
    {
        $user = auth()->user();
        $withdrawals = $user->withdrawals()->with('payoutAccount')->latest()->paginate(10);

        $isSellerRoute = $user->isSeller() && request()->routeIs('seller.withdrawals');

        return view('wallet.withdrawals', compact('withdrawals', 'isSellerRoute'));
    }

    public function storePayoutAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name' => ['required', 'string', 'max:40'],
            'account_number' => ['required', 'string', 'max:30'],
            'account_name' => ['required', 'string', 'max:60'],
        ], [], ['bank_name' => 'Bank', 'account_number' => 'Nomor rekening', 'account_name' => 'Nama pemilik']);

        $user = auth()->user();

        if ($user->payoutAccounts()->count() === 0) {
            PayoutAccount::query()->create($request->only('bank_name', 'account_number', 'account_name') + ['user_id' => $user->id, 'is_primary' => true]);
        } else {
            $user->payoutAccounts()->create($request->only('bank_name', 'account_number', 'account_name'));
        }

        return back()->with('status', 'Rekening tujuan penarikan ditambahkan.');
    }

    public function adminWithdrawals(): View
    {
        $withdrawals = Withdrawal::query()
            ->with(['user', 'payoutAccount'])
            ->latest()
            ->paginate(15);

        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function processWithdrawal(Withdrawal $withdrawal, Request $request): RedirectResponse
    {
        abort_unless($withdrawal->status === WithdrawalStatus::Pending, 403);

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = $withdrawal->user->wallet;
        $transaction = $withdrawal->walletTransaction;

        if ($request->string('action')->toString() === 'approve') {
            $withdrawal->update([
                'status' => WithdrawalStatus::Completed,
                'processed_at' => now(),
                'admin_note' => $request->string('admin_note')->toString(),
            ]);

            $transaction?->update(['status' => WalletTransactionStatus::Success]);

            Notifier::notify($withdrawal->user, 'wallet', 'Penarikan berhasil', "Penarikan {$withdrawal->reference} sebesar Rp ".number_format($withdrawal->amount, 0, ',', '.').' telah dikirim ke rekening kamu.');
        } else {
            $withdrawal->update([
                'status' => WithdrawalStatus::Rejected,
                'processed_at' => now(),
                'admin_note' => $request->string('admin_note')->toString(),
            ]);

            $total = $withdrawal->amount + $withdrawal->fee;

            $wallet->increment('available_balance', $total);
            $wallet->refresh();

            $transaction?->update(['status' => WalletTransactionStatus::Failed]);

            $wallet->transactions()->create([
                'type' => WalletTransactionType::Adjustment,
                'direction' => WalletDirection::In,
                'amount' => $total,
                'balance_after' => $wallet->available_balance + $wallet->escrow_balance,
                'description' => "Pengembalian dana penarikan ditolak ({$withdrawal->reference})",
                'status' => WalletTransactionStatus::Success,
            ]);

            Notifier::notify($withdrawal->user, 'wallet', 'Penarikan ditolak', "Penarikan {$withdrawal->reference} ditolak. Dana telah dikembalikan ke saldo kamu.");
        }

        return back()->with('status', 'Status penarikan diperbarui.');
    }
}
