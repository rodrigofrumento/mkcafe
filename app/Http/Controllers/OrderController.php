<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use App\Models\UserOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $userOrders = $request->user()->orders()->latest()->paginate(15);

        return view('user-orders', compact('userOrders'));
    }

    /**
     * Fecha o pedido a partir do carrinho. Não há gateway de pagamento:
     * o pedido é criado como pendente.
     */
    public function store(Request $request): RedirectResponse
    {
        $cart = session('cart', []);

        if (! $cart) {
            return redirect()->route('cart.index');
        }

        // Revalida os itens com os dados atuais do banco (preço, loja, existência).
        $products = Product::whereIn('slug', array_keys($cart))->get()->keyBy('slug');
        $items = [];

        foreach ($cart as $slug => $line) {
            if (! $product = $products->get($slug)) {
                continue;
            }

            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'store_id' => $product->store_id,
                'amount' => (int) $line['amount'],
            ];
        }

        if (! $items) {
            session()->forget('cart');
            flash('Os produtos do carrinho não estão mais disponíveis')->warning();

            return redirect()->route('cart.index');
        }

        $storeIds = array_values(array_unique(array_column($items, 'store_id')));

        DB::transaction(function () use ($request, $items, $storeIds) {
            $order = $request->user()->orders()->create([
                'reference' => (string) Str::uuid(),
                'status' => UserOrder::STATUS_PENDING,
                'items' => $items,
            ]);

            $order->stores()->sync($storeIds);
        });

        Store::notifyOwners($storeIds);

        session()->forget('cart');
        flash('Pedido criado com sucesso!')->success();

        return redirect()->route('user.orders');
    }
}
