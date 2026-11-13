<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);

        return view('cart', compact('cart'));
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product.slug' => ['required', 'string'],
            'product.amount' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        // Nome, preço e loja vêm sempre do banco, nunca do formulário.
        $product = Product::where('slug', $data['product']['slug'])->firstOrFail();
        $amount = (int) $data['product']['amount'];

        $cart = session('cart', []);

        if (isset($cart[$product->slug])) {
            $cart[$product->slug]['amount'] += $amount;
        } else {
            $cart[$product->slug] = [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'price' => (float) $product->price,
                'store_id' => $product->store_id,
                'amount' => $amount,
            ];
        }

        session(['cart' => $cart]);

        flash('Produto adicionado no carrinho')->success();

        return redirect()->route('product.single', ['slug' => $product->slug]);
    }

    public function remove(string $slug): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$slug]);

        $cart ? session(['cart' => $cart]) : session()->forget('cart');

        return redirect()->route('cart.index');
    }

    public function cancel(): RedirectResponse
    {
        session()->forget('cart');

        flash('Compra cancelada com sucesso')->success();

        return redirect()->route('cart.index');
    }
}
