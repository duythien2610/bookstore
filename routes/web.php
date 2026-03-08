<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\UserController;

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
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification (yêu cầu đăng nhập — Security Lớp 1)
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/verify-email/resend', [AuthController::class, 'resendCode'])->name('verification.resend');
    Route::get('/verification-success', function () {
        return view('auth.verification-success');
    })->name('verification.success');
});

// Forgot Password / Reset Password (2 bước)
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.send');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'verifyResetCode'])->name('password.verify-code');
Route::post('/reset-password/resend', [AuthController::class, 'resendResetCode'])->name('password.resend');
Route::get('/new-password', [AuthController::class, 'showNewPassword'])->name('password.new');
Route::post('/new-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ─── Public Pages ────────────────────────────────────────────────────────
Route::get('/products', function () {
    return view('pages.product-listing');
})->name('products.index');

Route::get('/products/{id}', function ($id = null) {
    return view('pages.product-detail');
})->name('products.show');

// Các route yêu cầu đăng nhập + đã xác thực email
Route::middleware('verified')->group(function () {
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

    Route::get('/profile', function () {
        return view('pages.profile');
    })->name('profile');
});

Route::get('/blog', function () {
    return view('pages.blog-listing');
})->name('blog.index');

Route::get('/blog/{slug}', function ($slug = null) {
    return view('pages.blog-detail');
})->name('blog.show');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// ─── Chatbot AI ──────────────────────────────────────────────────────────
Route::post('/chatbot/send', [ChatbotController::class, 'chat'])->name('chatbot.send');


// ─── Admin Routes (chỉ admin mới vào được) ───────────────────────────────
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', function () {
        $tongSach     = \App\Models\Sach::count();
        $tongDonHang  = \App\Models\DonHang::count();
        $tongKhachHang = \App\Models\User::count();
        $tongTheLoai  = \App\Models\TheLoai::count();
        $sachMoi      = \App\Models\Sach::with('tacGia')->orderByDesc('created_at')->take(5)->get();

        return view('admin.dashboard', compact('tongSach', 'tongDonHang', 'tongKhachHang', 'tongTheLoai', 'sachMoi'));
    })->name('admin.dashboard');

    Route::get('/inventory', [App\Http\Controllers\SachController::class, 'index'])->name('admin.inventory');

    Route::get('/orders', function () {
        $donHangs = \App\Models\DonHang::with('user')->orderByDesc('created_at')->get();
        return view('admin.orders', compact('donHangs'));
    })->name('admin.orders');

    Route::get('/books/create', [App\Http\Controllers\SachController::class, 'create'])->name('admin.books.create');
    Route::post('/books', [App\Http\Controllers\SachController::class, 'store'])->name('admin.books.store');
    Route::get('/books/{sach}/edit', [App\Http\Controllers\SachController::class, 'edit'])->name('admin.books.edit');
    Route::put('/books/{sach}', [App\Http\Controllers\SachController::class, 'update'])->name('admin.books.update');

    // Đối tác (Tác giả, NXB, NCC)
    Route::get('/partners', [App\Http\Controllers\TacGiaController::class, 'index'])->name('admin.partners');
    Route::get('/tac-gia/create', [App\Http\Controllers\TacGiaController::class, 'create'])->name('admin.tac-gia.create');
    Route::post('/tac-gia', [App\Http\Controllers\TacGiaController::class, 'store'])->name('admin.tac-gia.store');

    // Nhà xuất bản
    Route::get('/nha-xuat-ban/create', [App\Http\Controllers\NhaXuatBanController::class, 'create'])->name('admin.nha-xuat-ban.create');
    Route::post('/nha-xuat-ban', [App\Http\Controllers\NhaXuatBanController::class, 'store'])->name('admin.nha-xuat-ban.store');

    // Nhà cung cấp
    Route::get('/nha-cung-cap/create', [App\Http\Controllers\NhaCungCapController::class, 'create'])->name('admin.nha-cung-cap.create');
    Route::post('/nha-cung-cap', [App\Http\Controllers\NhaCungCapController::class, 'store'])->name('admin.nha-cung-cap.store');

    // Thể loại
    Route::get('/the-loai', [App\Http\Controllers\TheLoaiController::class, 'index'])->name('admin.the-loai.index');
    Route::get('/the-loai/create', [App\Http\Controllers\TheLoaiController::class, 'create'])->name('admin.the-loai.create');
    Route::post('/the-loai', [App\Http\Controllers\TheLoaiController::class, 'store'])->name('admin.the-loai.store');

    // Quản lý người dùng
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});
