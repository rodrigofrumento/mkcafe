<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_products_and_stores(): void
    {
        $product = Product::factory()->create(['name' => 'Câmera Analógica']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Câmera Analógica')
            ->assertSee($product->store->name);
    }

    public function test_product_page_shows_product(): void
    {
        $product = Product::factory()->create();

        $this->get(route('product.single', $product->slug))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_category_and_store_pages_render(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        $this->get(route('category.single', $category->slug))->assertOk()->assertSee($product->name);
        $this->get(route('store.single', $product->store->slug))->assertOk()->assertSee($product->name);
    }

    public function test_unknown_slugs_return_404(): void
    {
        $this->get('/product/nao-existe')->assertNotFound();
        $this->get('/category/nao-existe')->assertNotFound();
        $this->get('/store/nao-existe')->assertNotFound();
    }

    public function test_debug_routes_were_removed(): void
    {
        Store::factory()->create();

        $this->get('/model')->assertNotFound();
        $this->get('/not')->assertNotFound();
    }
}
