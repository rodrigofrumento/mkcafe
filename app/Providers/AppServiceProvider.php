<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Menu de categorias do layout da loja.
        View::composer('layouts.front', function ($view) {
            $view->with('categories', Category::orderBy('name')->get(['name', 'slug']));
        });
    }
}
