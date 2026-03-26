<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
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
    $sachMoi = Cache::remember('home_sach_moi', 3600, function () {
        return \App\Models\Sach::with('tacGia')
            ->where('so_luong_ton', '>', 0)
            ->orderByDesc('created_at')
            ->limit(8)->get();
    });

    $sachBanChay = Cache::remember('home_sach_ban_chay', 3600, function () {
        return \App\Models\Sach::with('tacGia')
            ->where('so_luong_ton', '>', 0)
            ->where('gia_goc', '>', 0)
            ->orderBy('so_luong_ton', 'asc')
            ->limit(8)->get();
    });

    $theLoais = Cache::remember('home_the_loais', 86400, function () {
        return \App\Models\TheLoai::whereNull('parent_id')
            ->orderBy('ten_the_loai')
            ->limit(6)->get();
    });

    $banners = Cache::remember('home_banners', 86400, function () {
        return \App\Models\Banner::where('trang_thai', true)
            ->where('vi_tri', 'hero')
            ->orderBy('thu_tu')
            ->orderByDesc('created_at')
            ->get();
    });

    return view('pages.home', compact('sachMoi', 'sachBanChay', 'theLoais', 'banners'));
})->name('home');

// ─── Auth Routes ─────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Giới hạn 5 lần/phút để tránh brute-force
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1'); // Giới hạn 3 lần đăng ký/phút
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('auth/google', [\App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\GoogleController::class, 'handleGoogleCallback']);

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/verify-email/resend', [AuthController::class, 'resendCode'])->name('verification.resend');
    Route::get('/verification-success', function () {
        return view('auth.verification-success');
    })->name('verification.success');
});

// Forgot Password / Reset Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('auth.forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.send');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'verifyResetCode'])->name('password.verify-code');
Route::post('/reset-password/resend', [AuthController::class, 'resendResetCode'])->name('password.resend');
Route::get('/new-password', [AuthController::class, 'showNewPassword'])->name('password.new');
Route::post('/new-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ─── Public Pages ────────────────────────────────────────────────────────
Route::get('/products', [App\Http\Controllers\SachController::class, 'indexPublic'])->name('products.index');
Route::get('/products/{id}', [App\Http\Controllers\SachController::class, 'showPublic'])->where('id', '[0-9]+')->name('products.show');

// Các route yêu cầu đăng nhập + đã xác thực email
Route::middleware('verified')->group(function () {
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'show'])->name('cart');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.coupon');

    Route::post('/checkout/prepare', [App\Http\Controllers\CheckoutController::class, 'prepare'])->name('checkout.prepare');
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:5,1'); // Tránh spam click thanh toán liên tục

    Route::get('/payos/return', [App\Http\Controllers\PayosController::class, 'handlePayosReturn'])->name('payos.return');

    Route::get('/order-success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('order.success');
    Route::get('/order-tracking/{id}', [App\Http\Controllers\CheckoutController::class, 'tracking'])->name('order.tracking');

    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'show'])->name('wishlist');
    Route::post('/wishlist/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // Đánh giá sách
    Route::post('/danh-gia', [App\Http\Controllers\DanhGiaController::class, 'store'])->name('danh-gia.store');
    Route::delete('/danh-gia/{id}', [App\Http\Controllers\DanhGiaController::class, 'destroy'])->name('danh-gia.destroy');
});

Route::get('/blog', function (\Illuminate\Http\Request $request) {
    $featuredPost = \App\Models\Post::where('status', 'published')
                        ->with('user')
                        ->orderBy('views', 'desc')
                        ->first();

    $query = \App\Models\Post::where('status', 'published')->with('user');

    if ($request->has('category') && $request->category !== 'Tất cả') {
        $query->where('category', $request->category);
    }

    $posts = $query->orderBy('created_at', 'desc')->paginate(9);
    $currentCategory = $request->get('category', 'Tất cả');

    return view('pages.blog-listing', compact('posts', 'currentCategory', 'featuredPost'));
})->name('blog.index');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// ─── Chatbot AI ──────────────────────────────────────────────────────────
Route::post('/chatbot/send', [ChatbotController::class, 'chat'])->name('chatbot.send');

// ─── PayOS Webhook (Must be outside CSRF middleware if possible, but let's check VerifyCsrfToken as well) ────────────────────
Route::post('/payos/webhook', [\App\Http\Controllers\PayosController::class, 'handleWebhook'])->name('payos.webhook');


// ─── Admin Routes ────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['admin', 'audit_log'])->group(function () {
    Route::get('/', function () {
        $tongSach     = \App\Models\Sach::count();
        $tongDonHang  = \App\Models\DonHang::count();
        $tongKhachHang = \App\Models\User::count();
        $tongTheLoai  = \App\Models\TheLoai::count();
        $sachMoi      = \App\Models\Sach::with('tacGia')->orderByDesc('created_at')->take(5)->get();

        return view('admin.dashboard', compact('tongSach', 'tongDonHang', 'tongKhachHang', 'tongTheLoai', 'sachMoi'));
    })->name('admin.dashboard');

    Route::get('/inventory', [App\Http\Controllers\SachController::class, 'index'])->name('admin.inventory');

    // Đơn hàng
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{id}/status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    Route::get('/books/create', [App\Http\Controllers\SachController::class, 'create'])->name('admin.books.create');
    Route::post('/books', [App\Http\Controllers\SachController::class, 'store'])->name('admin.books.store');
    Route::post('/books/import-json', [App\Http\Controllers\SachController::class, 'importJson'])->name('admin.books.import-json');
    Route::get('/books/{sach}/edit', [App\Http\Controllers\SachController::class, 'edit'])->name('admin.books.edit');
    Route::put('/books/{sach}', [App\Http\Controllers\SachController::class, 'update'])->name('admin.books.update');
    Route::delete('/books/{sach}', [App\Http\Controllers\SachController::class, 'destroy'])->name('admin.books.destroy');

    // Đối tác
    Route::get('/partners', [App\Http\Controllers\TacGiaController::class, 'index'])->name('admin.partners');
    Route::get('/tac-gia/create', [App\Http\Controllers\TacGiaController::class, 'create'])->name('admin.tac-gia.create');
    Route::post('/tac-gia', [App\Http\Controllers\TacGiaController::class, 'store'])->name('admin.tac-gia.store');
    Route::put('/tac-gia/{id}', [App\Http\Controllers\TacGiaController::class, 'update'])->name('admin.tac-gia.update');
    Route::delete('/tac-gia/{id}', [App\Http\Controllers\TacGiaController::class, 'destroy'])->name('admin.tac-gia.destroy');

    // Nhà xuất bản
    Route::get('/nha-xuat-ban/create', [App\Http\Controllers\NhaXuatBanController::class, 'create'])->name('admin.nha-xuat-ban.create');
    Route::post('/nha-xuat-ban', [App\Http\Controllers\NhaXuatBanController::class, 'store'])->name('admin.nha-xuat-ban.store');
    Route::put('/nha-xuat-ban/{id}', [App\Http\Controllers\NhaXuatBanController::class, 'update'])->name('admin.nha-xuat-ban.update');
    Route::delete('/nha-xuat-ban/{id}', [App\Http\Controllers\NhaXuatBanController::class, 'destroy'])->name('admin.nha-xuat-ban.destroy');

    // Nhà cung cấp
    Route::get('/nha-cung-cap/create', [App\Http\Controllers\NhaCungCapController::class, 'create'])->name('admin.nha-cung-cap.create');
    Route::post('/nha-cung-cap', [App\Http\Controllers\NhaCungCapController::class, 'store'])->name('admin.nha-cung-cap.store');
    Route::put('/nha-cung-cap/{id}', [App\Http\Controllers\NhaCungCapController::class, 'update'])->name('admin.nha-cung-cap.update');
    Route::delete('/nha-cung-cap/{id}', [App\Http\Controllers\NhaCungCapController::class, 'destroy'])->name('admin.nha-cung-cap.destroy');

    // Thể loại
    Route::get('/the-loai', [App\Http\Controllers\TheLoaiController::class, 'index'])->name('admin.the-loai.index');
    Route::get('/the-loai/create', [App\Http\Controllers\TheLoaiController::class, 'create'])->name('admin.the-loai.create');
    Route::post('/the-loai', [App\Http\Controllers\TheLoaiController::class, 'store'])->name('admin.the-loai.store');
    Route::put('/the-loai/{id}', [App\Http\Controllers\TheLoaiController::class, 'update'])->name('admin.the-loai.update');
    Route::delete('/the-loai/{id}', [App\Http\Controllers\TheLoaiController::class, 'destroy'])->name('admin.the-loai.destroy');

    // Quản lý người dùng
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Quản lý Blog
    Route::get('/blogs', [App\Http\Controllers\PostController::class, 'adminIndex'])->name('admin.blogs.index');
    Route::put('/blogs/{post}/approve', [App\Http\Controllers\PostController::class, 'approve'])->name('admin.blogs.approve');
    Route::put('/blogs/{post}/reject', [App\Http\Controllers\PostController::class, 'reject'])->name('admin.blogs.reject');

    // Mã giảm giá
    Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('admin.coupons.index');
    Route::post('/coupons', [App\Http\Controllers\CouponController::class, 'store'])->name('admin.coupons.store');
    Route::patch('/coupons/{id}/toggle', [App\Http\Controllers\CouponController::class, 'toggleStatus'])->name('admin.coupons.toggle');
    Route::delete('/coupons/{id}', [App\Http\Controllers\CouponController::class, 'destroy'])->name('admin.coupons.destroy');

    // Banner
    Route::get('/banners', [App\Http\Controllers\BannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/banners', [App\Http\Controllers\BannerController::class, 'store'])->name('admin.banners.store');
    Route::put('/banners/{id}', [App\Http\Controllers\BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{id}', [App\Http\Controllers\BannerController::class, 'destroy'])->name('admin.banners.destroy');
    Route::patch('/banners/{id}/toggle', [App\Http\Controllers\BannerController::class, 'toggleStatus'])->name('admin.banners.toggle');

    // Cài đặt Admin
    Route::get('/settings', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $tongSach = \App\Models\Sach::count();
        $tongDonHang = \App\Models\DonHang::count();
        $tongKhachHang = \App\Models\User::count();
        $tongTheLoai = \App\Models\TheLoai::count();
        return view('admin.settings', compact('user', 'tongSach', 'tongDonHang', 'tongKhachHang', 'tongTheLoai'));
    })->name('admin.settings');

    Route::put('/settings/password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed'    => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->new_password)]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    })->name('admin.settings.password');
});

// ─── Blog Management (User) ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/blog/create', [\App\Http\Controllers\PostController::class, 'create'])->name('blog.create');
    Route::post('/blog/store', [\App\Http\Controllers\PostController::class, 'store'])->name('blog.store');
    Route::post('/blog/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('blog.upload-image');
});

// Route blog/{slug} phải sau các route tĩnh
Route::get('/blog/{slug}', function ($slug) {
    $post = \App\Models\Post::where('slug', $slug)
                ->where('status', 'published')
                ->with('user')
                ->firstOrFail();

    $post->increment('views');

    $relatedPosts = \App\Models\Post::where('status', 'published')
                        ->where('category', $post->category)
                        ->where('id', '!=', $post->id)
                        ->with('user')
                        ->orderBy('views', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();

    if ($relatedPosts->count() < 3) {
        $extra = \App\Models\Post::where('status', 'published')
                     ->where('id', '!=', $post->id)
                     ->whereNotIn('id', $relatedPosts->pluck('id'))
                     ->with('user')
                     ->orderBy('views', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->limit(3 - $relatedPosts->count())
                     ->get();
        $relatedPosts = $relatedPosts->merge($extra);
    }

    return view('pages.blog-detail', compact('post', 'relatedPosts'));
})->name('blog.show');
