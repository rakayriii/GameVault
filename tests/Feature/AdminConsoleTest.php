<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Game;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConsoleTest extends TestCase
{
    use RefreshDatabase;

    private function payroll(User $user, int $balance = 0, int $escrow = 0): Wallet
    {
        return Wallet::query()->create([
            'user_id' => $user->id,
            'available_balance' => $balance,
            'escrow_balance' => $escrow,
        ]);
    }

    private function makeOrder(?OrderStatus $status = OrderStatus::Escrow): Order
    {
        $buyer = User::factory()->asBuyer()->create();
        $seller = User::factory()->asSeller()->create();
        $game = Game::factory()->create();
        $account = GameAccount::factory()->approved()->for($game)->create([
            'seller_id' => $seller->id,
            'price' => 1_000_000,
            'strike_price' => null,
            'discount_percent' => null,
        ]);

        return Order::query()->create([
            'order_no' => 'GV-TEST-'.strtoupper(uniqid()),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'game_account_id' => $account->id,
            'subtotal' => 1_000_000,
            'discount_amount' => 0,
            'fee_amount' => 100_000,
            'total_amount' => 1_100_000,
            'payment_method' => PaymentMethod::Wallet->value,
            'status' => $status,
        ]);
    }

    public function test_staff_can_open_the_most_recent_order(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $order = $this->makeOrder();

        $this->actingAs($admin)->get(route('admin.orders'))->assertOk()->assertSee($order->order_no);
        $this->actingAs($admin)->get(route('orders.show', $order))->assertOk();
    }

    public function test_arbitrator_can_access_admin_pages(): void
    {
        $staff = User::factory()->asArbitrator()->create();

        $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($staff)->get(route('admin.orders'))->assertOk();
        $this->actingAs($staff)->get(route('admin.rekber'))->assertOk();
    }

    public function test_non_staff_cannot_access_admin_pages(): void
    {
        $buyer = User::factory()->asBuyer()->create();

        $this->actingAs($buyer)->get(route('admin.orders'))->assertForbidden();
        $this->actingAs($buyer)->get(route('admin.rekber'))->assertForbidden();
        $this->actingAs($buyer)->get(route('admin.customers'))->assertForbidden();
    }

    public function test_admin_orders_can_be_filtered_by_status_and_searched(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $escrow = $this->makeOrder(OrderStatus::Escrow);
        $this->makeOrder(OrderStatus::Completed);

        $this->actingAs($admin)
            ->get(route('admin.orders', ['status' => 'escrow']))
            ->assertOk()
            ->assertSee($escrow->order_no);

        $this->actingAs($admin)
            ->get(route('admin.orders', ['q' => $escrow->order_no]))
            ->assertOk()
            ->assertSee($escrow->order_no);
    }

    public function test_admin_customers_page_shows_wallet_summary(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $buyer = User::factory()->asBuyer()->create(['is_verified' => true]);
        $this->payroll($buyer, 250_000, 500_000);

        $this->actingAs($admin)->get(route('admin.customers'))
            ->assertOk()
            ->assertSee($buyer->username);
    }

    public function test_admin_rekber_vault_page_shows_escrow_stats(): void
    {
        $admin = User::factory()->asAdmin()->create();
        $buyer = User::factory()->asBuyer()->create();
        $wallet = $this->payroll($buyer, 1_000_000, 500_000);
        $order = $this->makeOrder(OrderStatus::Handover);

        WalletTransaction::query()->create([
            'wallet_id' => $wallet->id,
            'type' => 'escrow_hold',
            'direction' => 'out',
            'amount' => 500_000,
            'balance_after' => 1_500_000,
            'description' => "Dana tahan escrow untuk #{$order->order_no}",
            'status' => 'success',
        ]);

        $this->actingAs($admin)->get(route('admin.rekber'))
            ->assertOk()
            ->assertSee($order->order_no)
            ->assertSee('1.500.000');
    }
}
