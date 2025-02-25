<?php

use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Public routes
// Route::get('/', function () {
//     return redirect('/login');
// });

// Route::get('/login', [ClientLoginController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [ClientLoginController::class, 'login']);
// Route::post('/logout', [ClientLoginController::class, 'logout'])->name('logout');

// // Protected client routes
// Route::middleware('auth')->group(function () {
//     Route::get('/products', [ProductController::class, 'index']);
//     Route::get('/products/{id}', [ProductController::class, 'show']);
//     Route::post('/cart-items', [CartController::class, 'store'])->name('cart.store');
// });

// // Admin routes (Filament will handle its own auth)
// Route::get('/', function () {
//     return redirect('/admin'); // Redirect to Filament's login page
// });




Route::get('/login', [ClientLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [ClientLoginController::class, 'login']);
Route::post('/logout', [ClientLoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.store');
    // Route::post('/cart/items', [CartController::class, 'store']); // Moved from api to web
});


    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.store');

Route::get('/', function () {
    return redirect('/admin');
});
