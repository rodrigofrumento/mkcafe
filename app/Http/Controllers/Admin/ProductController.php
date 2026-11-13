<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $store = $request->user()->store;

        if (! $store) {
            flash('É preciso criar loja para criar produtos')->warning();

            return redirect()->route('admin.stores.index');
        }

        $products = $store->products()->latest('id')->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->user()->store) {
            flash('É preciso criar loja para criar produtos')->warning();

            return redirect()->route('admin.stores.index');
        }

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $store = $request->user()->store;
        abort_unless($store, 403);

        $product = $store->products()->create($request->safe()->except(['categories', 'photos']));
        $product->categories()->sync($request->validated('categories', []));
        $this->savePhotos($request, $product);

        flash('Produto Criado com Sucesso!')->success();

        return redirect()->route('admin.products.index');
    }

    public function edit(Request $request, int $product): View
    {
        $product = $this->ownProduct($request, $product);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, int $product): RedirectResponse
    {
        $product = $this->ownProduct($request, $product);

        $product->update($request->safe()->except(['categories', 'photos']));
        $product->categories()->sync($request->validated('categories', []));
        $this->savePhotos($request, $product);

        flash('Produto Atualizado com Sucesso!')->success();

        return redirect()->route('admin.products.index');
    }

    public function destroy(Request $request, int $product): RedirectResponse
    {
        $product = $this->ownProduct($request, $product);

        Storage::disk('public')->delete($product->photos->pluck('image')->all());
        $product->delete();

        flash('Produto Removido com Sucesso!')->success();

        return redirect()->route('admin.products.index');
    }

    /** Só produtos da loja do próprio usuário; qualquer outro ID responde 404. */
    private function ownProduct(Request $request, int $id): Product
    {
        $store = $request->user()->store;
        abort_unless($store, 404);

        return $store->products()->findOrFail($id);
    }

    private function savePhotos(ProductRequest $request, Product $product): void
    {
        foreach ($request->file('photos', []) as $photo) {
            $product->photos()->create(['image' => $photo->store('products', 'public')]);
        }
    }
}
