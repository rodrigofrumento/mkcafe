<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [HomeController::class, 'single'])->name('product.single');
Route::get('/category/{slug}', [CategoryController::class, 'index'])->name('category.single');
Route::get('/store/{slug}', [StoreController::class, 'index'])->name('store.single');

Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('add', 'add')->name('add');
    Route::delete('remove/{slug}', 'remove')->name('remove');
    Route::post('cancel', 'cancel')->name('cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('my-orders', [OrderController::class, 'index'])->name('user.orders');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
});

Route::middleware(['auth', 'store.owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [Admin\NotificationController::class, 'readAll'])->name('notifications.read.all');
    Route::post('notifications/{notification}/read', [Admin\NotificationController::class, 'read'])->name('notifications.read');

    Route::resource('stores', Admin\StoreController::class)->except('show');
    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::delete('photos/{photo}', [Admin\ProductPhotoController::class, 'destroy'])->name('photo.remove');
    Route::get('orders/my', [Admin\OrdersController::class, 'index'])->name('orders.my');
});

Auth::routes(['verify' => false, 'confirm' => false]);
