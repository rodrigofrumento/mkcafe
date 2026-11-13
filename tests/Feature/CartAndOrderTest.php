<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\UserOrder;
use App\Notifications\StoreReceiveNewOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CartAndOrderTest extends TestCase
{
    use RefreshDatabase;

    private function addToCart(Product $product, int $amount = 1, array $extra = [])
    {
        return $this->post(route('cart.add'), [
            'product' => array_merge(['slug' => $product->slug, 'amount' => $amount], $extra),
        ]);
    }

    public function test_adds_product_to_cart_using_price_from_database(): void
    {
        $product = Product::factory()->create(['price' => 150.00]);

        // Tenta adulterar preço e nome pelo formulário.
        $this->addToCart($product, 2, ['price' => 0.01, 'name' => 'Hack'])
            ->assertRedirect(route('product.single', $product->slug));

        $item = session('cart')[$product->slug];
        $this->assertSame(150.0, $item['price']);
        $this->assertSame($product->name, $item['name']);
        $this->assertSame(2, $item['amount']);
    }

    public function test_cart_page_lists_items_and_total(): void
    {
        $product = Product::factory()->create(['price' => 25]);
        $this->addToCart($product, 2);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('R$ 50,00')
            ->assertSee(route('orders.store'), false);
    }

    public function test_adding_same_product_twice_increments_amount(): void
    {
        $product = Product::factory()->create();

        $this->addToCart($product, 1);
        $this->addToCart($product, 3);

        $this->assertSame(4, session('cart')[$product->slug]['amount']);
    }

    public function test_rejects_invalid_amounts(): void
    {
        $product = Product::factory()->create();

        $this->addToCart($product, 0)->assertSessionHasErrors('product.amount');
        $this->addToCart($product, -5)->assertSessionHasErrors('product.amount');
        $this->assertNull(session('cart'));
    }

    public function test_remove_and_cancel(): void
    {
        $a = Product::factory()->create();
        $b = Product::factory()->create();
        $this->addToCart($a);
        $this->addToCart($b);

        $this->delete(route('cart.remove', $a->slug))->assertRedirect(route('cart.index'));
        $this->assertSame([$b->slug], array_keys(session('cart')));

        $this->post(route('cart.cancel'))->assertRedirect(route('cart.index'));
        $this->assertNull(session('cart'));
    }

    public function test_cart_state_changes_do_not_accept_get(): void
    {
        $product = Product::factory()->create();

        $this->get('/cart/remove/'.$product->slug)->assertMethodNotAllowed();
        $this->get('/cart/cancel')->assertMethodNotAllowed();
    }

    public function test_guest_must_login_to_place_order(): void
    {
        $this->addToCart(Product::factory()->create());

        $this->post(route('orders.store'))->assertRedirect(route('login'));
        $this->assertDatabaseCount('user_orders', 0);
    }

    public function test_places_order_and_notifies_store_owners(): void
    {
        Notification::fake();

        $customer = User::factory()->create();
        $p1 = Product::factory()->create(['price' => 10]);
        $p2 = Product::factory()->create(['price' => 20]);

        $this->actingAs($customer);
        $this->addToCart($p1, 2);
        $this->addToCart($p2, 1);

        $this->post(route('orders.store'))->assertRedirect(route('user.orders'));

        $order = UserOrder::sole();
        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame(UserOrder::STATUS_PENDING, $order->status);
        $this->assertEquals(40.0, $order->total());
        $this->assertEqualsCanonicalizing(
            [$p1->store_id, $p2->store_id],
            $order->stores()->pluck('stores.id')->all(),
        );
        $this->assertNull(session('cart'));

        Notification::assertSentTo([$p1->store->user, $p2->store->user], StoreReceiveNewOrder::class);

        $this->get(route('user.orders'))->assertOk()->assertSee($order->reference)->assertSee($p1->name);
    }

    public function test_order_uses_current_database_price(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['price' => 10]);

        $this->actingAs($customer);
        $this->addToCart($product, 1);
        $product->update(['price' => 99]);

        $this->post(route('orders.store'));

        $this->assertEquals(99.0, UserOrder::sole()->items[0]['price']);
    }

    public function test_empty_cart_does_not_create_order(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('orders.store'))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('user_orders', 0);
    }
}
