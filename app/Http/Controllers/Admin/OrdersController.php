<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class OrdersController extends Controller
{
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        $orders = $store
            ? $store->orders()->latest('user_orders.id')->paginate(15)
            : new LengthAwarePaginator([], 0, 15);

        return view('admin.orders.index', compact('orders', 'store'));
    }
}
