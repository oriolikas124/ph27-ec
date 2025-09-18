<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SampleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/sample', [SampleController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [ProductController::class, 'cartIndex'])->name('cart.index');
Route::post('/cart', [ProductController::class, 'cartStore'])->name('cart.store');
Route::post('/cart/update', [ProductController::class, 'cartUpdate'])->name('cart.update');
Route::post('/cart/remove', [ProductController::class, 'cartRemove'])->name('cart.remove');
Route::post('/cart/clear', [ProductController::class, 'cartClear'])->name('cart.clear');

Route::get('/checkout/shipping', [ProductController::class, 'shippingForm'])->name('checkout.shipping');
Route::post('/checkout/shipping', [ProductController::class, 'storeShipping'])->name('checkout.shipping.store');
Route::get('/checkout/review', [ProductController::class, 'review'])->name('checkout.review');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
    Route::post('/order', [OrderController::class, 'order'])->name('order');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
