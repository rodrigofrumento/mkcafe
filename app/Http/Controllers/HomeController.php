<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::with('photos')->latest('id')->limit(6)->get();
        $stores = Store::latest('id')->limit(3)->get();

        return view('welcome', compact('products', 'stores'));
    }

    public function single(string $slug): View
    {
        $product = Product::with(['photos', 'store'])->where('slug', $slug)->firstOrFail();

        return view('single', compact('product'));
    }
}
