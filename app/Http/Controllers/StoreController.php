<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(string $slug): View
    {
        $store = Store::with('products.photos')->where('slug', $slug)->firstOrFail();

        return view('store', compact('store'));
    }
}
