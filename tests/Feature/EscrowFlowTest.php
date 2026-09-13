<?php

namespace Tests\Feature;

use App\Enums\ListingStatus;
use App\Enums\OrderStatus;
use App\Enums\SellerRequestStatus;
use App\Enums\UserRole;
use App\Models\Game;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EscrowFlowTest extends TestCase
{
    use RefreshDatabase;

    private function walletFor(User $user, int $balance = 0, int $escrow = 0): Wallet
    {
        return Wallet::query()->create([
            'user_id' => $user->id,
            'available_balance' => $balance,
            'escrow_balance' => $escrow,
        ]);
    }

    private function makeAccount(User $seller, int $price = 1_000_000): GameAccount
    {
        $game = Game::factory()->create();

        return GameAccount::factory()->approved()->for($game)->create([
            'seller_id' => $seller->id,
            'price' => $price,
            'strike_price' => null,
            'discount_percent' => null,
        ]);
    }

    public function test_full_escrow_flow_with_wallet_payment(): void
    {
        $buyer = User::factory()->asBuyer()->create();
        $seller = User::factory()->asSeller()->create();
        SellerProfile::factory()->for($seller, 'seller')->create();
        $this->walletFor($buyer, 10_000_000);
        $this->walletFor($seller);

        $account = $this->makeAccount($seller, 1_000_000);

        $this->actingAs($buyer)->get(route('checkout.create', $account))->assertOk();

        $this->actingAs($buyer)->post(route('checkout.store', $account), [
            'payment_method' => 'wallet',
            'agree_escrow' => '1',
        ])->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame(0, $order->fee_amount);

        $this->actingAs($buyer)->post(route('checkout.mark-paid', $order))->assertRedirect(route('orders.handover', $order));

        $order->refresh();
        $this->assertSame(OrderStatus::Escrow, $order->status);
        $this->assertSame(9_000_000, $buyer->wallet->refresh()->available_balance);
        $this->assertSame(1_000_000, $buyer->wallet->escrow_balance);

        $this->actingAs($seller)->post(route('orders.message', $order), [
            'body' => 'email: seller@test.com password: rahasia123',
            'credential' => '1',
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(OrderStatus::Handover, $order->status);

        $this->actingAs($buyer)->post(route('checkout.confirm', $order), ['confirm_check' => '1'])
            ->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertSame(0, $buyer->wallet->refresh()->escrow_balance);
        $this->assertSame(1_000_000, $seller->wallet->refresh()->available_balance);
        $this->assertSame(ListingStatus::Sold, $account->refresh()->status);

        $this->actingAs($buyer)->post(route('orders.review', $order), ['rating' => 5, 'content' => 'Mantap'])->assertRedirect();
        $this->assertDatabaseHas('reviews', ['order_id' => $order->id, 'rating' => 5]);
    }

    public function test_gateway_payment_charges_five_percent_fee(): void
    {
        $buyer = User::factory()->asBuyer()->create();
        $seller = User::factory()->asSeller()->create();
        SellerProfile::factory()->for($seller, 'seller')->create();
        $account = $this->makeAccount($seller, 2_000_000);

        $this->actingAs($buyer)->post(route('checkout.store', $account), [
            'payment_method' => 'qris',
            'agree_escrow' => '1',
        ])->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertSame(100_000, $order->fee_amount);
        $this->assertSame(2_100_000, $order->total_amount);
    }

    public function test_seller_can_request_and_admin_can_approve(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $applicant = User::factory()->asBuyer()->create();

        $this->actingAs($applicant)->post(route('seller.request.submit'), [
            'store_name' => 'Toko Keren',
            'reason' => 'Saya ingin berjualan dengan aman.',
        ])->assertRedirect();

        $request = $applicant->sellerRequests()->firstOrFail();
        $this->assertSame(SellerRequestStatus::Pending, $request->status);

        $this->actingAs($admin)->post(route('admin.seller-requests.review', $request), [
            'action' => 'approve',
        ])->assertRedirect();

        $this->assertSame(UserRole::Seller, $applicant->refresh()->role);
        $this->assertDatabaseHas('seller_profiles', ['user_id' => $applicant->id, 'store_name' => 'Toko Keren']);
    }

    public function test_non_seller_is_redirected_from_seller_center(): void
    {
        $buyer = User::factory()->asBuyer()->create();

        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertRedirect(route('seller.request'));
    }

    public function test_buyer_can_cancel_and_get_refund_from_escrow(): void
    {
        $buyer = User::factory()->asBuyer()->create();
        $seller = User::factory()->asSeller()->create();
        SellerProfile::factory()->for($seller, 'seller')->create();
        $this->walletFor($buyer, 5_000_000);
        $this->walletFor($seller);
        $account = $this->makeAccount($seller, 1_000_000);

        $this->actingAs($buyer)->post(route('checkout.store', $account), [
            'payment_method' => 'wallet',
            'agree_escrow' => '1',
        ]);
        $order = Order::query()->firstOrFail();
        $this->actingAs($buyer)->post(route('checkout.mark-paid', $order));

        $this->actingAs($buyer)->post(route('orders.cancel', $order))->assertRedirect();

        $order->refresh();
        $this->assertSame(OrderStatus::Cancelled, $order->status);
        $this->assertSame(5_000_000, $buyer->wallet->refresh()->available_balance);
        $this->assertSame(0, $buyer->wallet->escrow_balance);
    }

    public function test_dispute_resolved_in_favor_of_buyer_refunds_escrow(): void
    {
        $buyer = User::factory()->asBuyer()->create();
        $seller = User::factory()->asSeller()->create();
        SellerProfile::factory()->for($seller, 'seller')->create();
        $staff = User::factory()->asArbitrator()->create();
        $this->walletFor($buyer, 5_000_000);
        $this->walletFor($seller);
        $account = $this->makeAccount($seller, 1_000_000);

        $this->actingAs($buyer)->post(route('checkout.store', $account), ['payment_method' => 'wallet', 'agree_escrow' => '1']);
        $order = Order::query()->firstOrFail();
        $this->actingAs($buyer)->post(route('checkout.mark-paid', $order));

        $this->actingAs($buyer)->post(route('orders.dispute', $order), [
            'category' => 'account_not_match',
            'description' => 'Akun tidak sesuai deskripsi.',
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(OrderStatus::Disputed, $order->status);
        $dispute = $order->dispute()->firstOrFail();

        $this->actingAs($staff)->post(route('admin.disputes.resolve', $dispute), [
            'decision' => 'buyer_win',
            'resolution_note' => 'Kredensial tidak valid.',
        ])->assertRedirect();

        $order->refresh();
        $this->assertSame(OrderStatus::Refunded, $order->status);
        $this->assertSame(5_000_000, $buyer->wallet->refresh()->available_balance);
        $this->assertSame(0, $buyer->wallet->escrow_balance);
        $this->assertSame('resolved', $dispute->refresh()->status->value);
    }

    public function test_admin_can_approve_and_reject_listing(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $seller = User::factory()->asSeller()->create();
        SellerProfile::factory()->for($seller, 'seller')->create();
        $account = GameAccount::factory()->pending()->create(['seller_id' => $seller->id]);

        $this->actingAs($admin)->post(route('admin.accounts.approve', $account), ['next' => '1'])->assertRedirect();
        $this->assertSame(ListingStatus::Approved, $account->fresh()->status);

        $account2 = GameAccount::factory()->pending()->create(['seller_id' => $seller->id]);
        $this->actingAs($admin)->post(route('admin.accounts.reject', $account2), ['reason' => 'Foto tidak jelas'])
            ->assertRedirect();
        $this->assertSame(ListingStatus::Rejected, $account2->fresh()->status);
        $this->assertSame('Foto tidak jelas', $account2->fresh()->rejection_reason);
    }

    public function test_wallet_withdraw_requires_two_factor_and_refunds_when_rejected(): void
    {
        $buyer = User::factory()->asBuyer()->create();
        $this->walletFor($buyer, 500_000);
        $admin = User::factory()->asAdmin()->create();
        $bank = $buyer->payoutAccounts()->create(['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_name' => $buyer->name, 'is_primary' => true]);

        $this->actingAs($buyer)->post(route('wallet.withdraw'), [
            'amount' => 100_000,
            'payout_account_id' => $bank->id,
        ])->assertRedirect();

        $this->assertSame(500_000, $buyer->wallet->refresh()->available_balance);

        $buyer->update(['two_factor_enabled' => true]);

        $this->actingAs($buyer)->post(route('wallet.withdraw'), [
            'amount' => 100_000,
            'payout_account_id' => $bank->id,
        ])->assertRedirect();

        $this->assertSame(399_000, $buyer->wallet->refresh()->available_balance);

        $withdrawal = $buyer->withdrawals()->firstOrFail();
        $this->actingAs($admin)->post(route('admin.withdrawals.process', $withdrawal), [
            'action' => 'reject',
            'admin_note' => 'Rekening tidak valid',
        ])->assertRedirect();

        $this->assertSame(500_000, $buyer->wallet->refresh()->available_balance);
    }
}
