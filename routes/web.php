<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Modtra Books
|--------------------------------------------------------------------------
*/

// ─── Homepage ────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('pages.home');
})->name('home');

// ─── Auth Routes ─────────────────────────────────────────────────────────
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password/{token?}', function ($token = null) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::get('/verify-email', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/verification-success', function () {
    return view('auth.verification-success');
})->name('verification.success');

// ─── Public Pages ────────────────────────────────────────────────────────
Route::get('/products', function () {
    return view('pages.product-listing');
})->name('products.index');

Route::get('/products/{id}', function ($id = null) {
    return view('pages.product-detail');
})->name('products.show');

Route::get('/cart', function () {
    return view('pages.cart');
})->name('cart');

Route::get('/checkout', function () {
    return view('pages.checkout');
})->name('checkout');

Route::get('/order-success', function () {
    return view('pages.order-success');
})->name('order.success');

Route::get('/order-tracking', function () {
    return view('pages.order-tracking');
})->name('order.tracking');

Route::get('/wishlist', function () {
    return view('pages.wishlist');
})->name('wishlist');

Route::get('/blog', function () {
    return view('pages.blog-listing');
})->name('blog.index');

Route::get('/blog/{slug}', function ($slug = null) {
    return view('pages.blog-detail');
})->name('blog.show');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/profile', function () {
    return view('pages.profile');
})->name('profile');

// ─── Admin Routes ────────────────────────────────────────────────────────
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/inventory', function () {
        return view('admin.inventory');
    })->name('admin.inventory');

    Route::get('/orders', function () {
        return view('admin.orders');
    })->name('admin.orders');
});
