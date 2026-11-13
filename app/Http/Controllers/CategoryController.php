<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(string $slug): View
    {
        $category = Category::with('products.photos')->where('slug', $slug)->firstOrFail();

        return view('category', compact('category'));
    }
}
