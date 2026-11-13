<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Notifications\StoreReceiveNewOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private Store $myStore;

    private Store $otherStore;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->myStore = Store::factory()->create();
        $this->otherStore = Store::factory()->create();
        $this->actingAs($this->myStore->user);
    }

    public function test_guests_and_customers_cannot_access_admin(): void
    {
        auth()->logout();
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create());
        $this->get(route('admin.products.index'))->assertRedirect(route('user.orders'));
        $this->get(route('admin.categories.index'))->assertRedirect(route('user.orders'));
    }

    public function test_user_without_role_cannot_access_admin(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'QUALQUER_COISA'])->save();

        $this->actingAs($user)->get(route('admin.stores.index'))->assertRedirect(route('user.orders'));
    }

    public function test_admin_pages_render_for_owner(): void
    {
        Product::factory()->for($this->myStore)->create();

        foreach (['admin.stores.index', 'admin.products.index', 'admin.products.create',
            'admin.categories.index', 'admin.categories.create', 'admin.orders.my',
            'admin.notifications.index'] as $route) {
            $this->get(route($route))->assertOk();
        }

        $this->get(route('admin.stores.edit', $this->myStore))->assertOk();
    }

    public function test_product_edit_page_shows_formatted_price_and_photos(): void
    {
        $product = Product::factory()->for($this->myStore)->create(['price' => 1234.5]);
        $photo = $product->photos()->create(['image' => 'products/foto.jpg']);

        $this->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('1.234,50')
            ->assertSee(route('admin.photo.remove', $photo), false);
    }

    public function test_owner_creates_product_with_brazilian_price_and_photo(): void
    {
        $category = Category::factory()->create();

        $this->post(route('admin.products.store'), [
            'name' => 'Lente 50mm',
            'description' => 'Lente fixa 50mm f/1.8, ótima para retratos e uso geral.',
            'body' => 'Detalhes da lente.',
            'price' => '1.234,56',
            'categories' => [$category->id],
            'photos' => [UploadedFile::fake()->image('lente.jpg')],
        ])->assertRedirect(route('admin.products.index'));

        $product = $this->myStore->products()->sole();
        $this->assertEquals('1234.56', $product->price);
        $this->assertSame([$category->id], $product->categories()->pluck('categories.id')->all());
        Storage::disk('public')->assertExists($product->photos()->sole()->image);
    }

    public function test_rejects_non_image_upload(): void
    {
        $this->post(route('admin.products.store'), [
            'name' => 'Produto',
            'description' => str_repeat('a', 40),
            'body' => 'x',
            'price' => '10,00',
            'photos' => [UploadedFile::fake()->create('script.php', 1, 'application/x-php')],
        ])->assertSessionHasErrors('photos.0');
    }

    public function test_owner_cannot_touch_products_from_another_store(): void
    {
        $foreign = Product::factory()->for($this->otherStore)->create(['name' => 'Original']);

        $this->get(route('admin.products.edit', $foreign))->assertNotFound();
        $this->put(route('admin.products.update', $foreign), [
            'name' => 'Invadido',
            'description' => str_repeat('a', 40),
            'body' => 'x',
            'price' => '1,00',
        ])->assertNotFound();
        $this->delete(route('admin.products.destroy', $foreign))->assertNotFound();

        $this->assertSame('Original', $foreign->fresh()->name);
    }

    public function test_owner_cannot_touch_another_store(): void
    {
        $this->get(route('admin.stores.edit', $this->otherStore))->assertNotFound();
        $this->put(route('admin.stores.update', $this->otherStore), [
            'name' => 'Invadida',
            'description' => 'Descrição alterada',
            'phone' => '(11) 1111-1111',
            'mobile_phone' => '(11) 11111-1111',
        ])->assertNotFound();
        $this->delete(route('admin.stores.destroy', $this->otherStore))->assertNotFound();

        $this->assertModelExists($this->otherStore);
        $this->assertNotSame('Invadida', $this->otherStore->fresh()->name);
    }

    public function test_owner_cannot_delete_photo_from_another_store(): void
    {
        $foreign = Product::factory()->for($this->otherStore)->create();
        $path = UploadedFile::fake()->image('x.jpg')->store('products', 'public');
        $photo = $foreign->photos()->create(['image' => $path]);

        $this->delete(route('admin.photo.remove', $photo))->assertNotFound();

        $this->assertModelExists($photo);
        Storage::disk('public')->assertExists($path);
    }

    public function test_owner_deletes_own_photo(): void
    {
        $product = Product::factory()->for($this->myStore)->create();
        $path = UploadedFile::fake()->image('x.jpg')->store('products', 'public');
        $photo = $product->photos()->create(['image' => $path]);

        $this->delete(route('admin.photo.remove', $photo))
            ->assertRedirect(route('admin.products.edit', $product));

        $this->assertModelMissing($photo);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_with_store_cannot_create_second_store(): void
    {
        $this->get(route('admin.stores.create'))->assertRedirect(route('admin.stores.index'));
    }

    public function test_owner_without_store_creates_one(): void
    {
        $this->actingAs(User::factory()->owner()->create());

        $this->post(route('admin.stores.store'), [
            'name' => 'Nova Loja',
            'description' => 'Loja de equipamentos',
            'phone' => '(11) 1111-1111',
            'mobile_phone' => '(11) 11111-1111',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect(route('admin.stores.index'));

        $store = Store::where('name', 'Nova Loja')->sole();
        $this->assertSame('nova-loja', $store->slug);
        Storage::disk('public')->assertExists($store->logo);
    }

    public function test_notifications_are_marked_as_read_only_via_post(): void
    {
        $owner = $this->myStore->user;
        $owner->notify(new StoreReceiveNewOrder);
        $notification = $owner->unreadNotifications()->sole();

        $this->get('/admin/notifications/'.$notification->id.'/read')->assertMethodNotAllowed();

        $this->post(route('admin.notifications.read', $notification->id));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_cannot_mark_someone_elses_notification(): void
    {
        $other = $this->otherStore->user;
        $other->notify(new StoreReceiveNewOrder);
        $notification = $other->unreadNotifications()->sole();

        $this->post(route('admin.notifications.read', $notification->id))->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);
    }
}
