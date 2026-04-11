<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;

use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\BannerController;




/*
|--------------------------------------------------------------------------
| Web Routes — Modtra Books
|--------------------------------------------------------------------------
*/

// ─── Homepage ────────────────────────────────────────────────────────────
Route::get('/', function () {
    // Sách nổi bật (8 cuốn): dùng cho section "Sách nổi bật"
    $sachNoiBat = \App\Models\Sach::mostSold(8)->with(['tacGia'])->get();

    // Mã giảm giá chung đang hoạt động (chỉ áp dụng toàn bộ - pham_vi = 'all')
    $activeCoupons = \App\Models\MaGiamGia::where('trang_thai', 1)
        ->where('pham_vi', 'all')
        ->where(function ($q) {
            $q->whereNull('ngay_het_han')->orWhere('ngay_het_han', '>=', now());
        })
        ->where(function ($q) {
            $q->whereNull('so_luong')->orWhereRaw('da_dung < so_luong');
        })
        ->orderBy('gia_tri', 'desc')
        ->first();

    // Thể loại phổ biến từ DB (parent only, tối đa 6)
    $theLoais = \App\Models\TheLoai::whereNull('parent_id')
        ->orderBy('ten_the_loai')
        ->limit(6)
        ->get();

    // Sách bán chạy theo từng thể loại cho homepage ranking widget
    $allCats = \App\Models\TheLoai::whereNull('parent_id')->orderBy('ten_the_loai')->get();
    $rankingByCategory = [];
    foreach ($allCats as $tl) {
        $childIds = $tl->children()->pluck('id')->toArray();
        $allIds   = array_merge([$tl->id], $childIds);
        $top = \App\Models\Sach::with(['tacGia', 'nhaXuatBan'])
            ->whereIn('the_loai_id', $allIds)
            ->withSum([
                'chiTiets as tong_ban' => function ($q) {
                    $q->whereHas('donHang', function ($dq) {
                        $dq->whereIn('trang_thai', ['da_giao', 'hoan_thanh']);
                    });
                }
            ], 'so_luong')
            ->orderByDesc('tong_ban')
            ->take(8)
            ->get();
        if ($top->isNotEmpty()) {
            $rankingByCategory[] = [
                'id'    => $tl->id,
                'name'  => $tl->ten_the_loai,
                'books' => $top,
            ];
        }
    }

    // Fallback nếu chưa có đơn hàng nào
    $sachBanChay = \App\Models\Sach::mostSold(8)->with(['tacGia', 'nhaXuatBan'])->get();

    return view('pages.home', compact('sachNoiBat', 'sachBanChay', 'rankingByCategory', 'activeCoupons', 'theLoais'));
})->name('home');

// ─── Auth Routes ─────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Google OAuth ─────────────────────────────────────────────────────────
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

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
Route::get('/products', [App\Http\Controllers\SachController::class, 'list'])->name('products.index');
Route::get('/featured-books', [App\Http\Controllers\SachController::class, 'featured'])->name('products.featured');
Route::get('/best-selling', [App\Http\Controllers\SachController::class, 'bestSelling'])->name('products.bestselling');

Route::get('/products/{id}', [App\Http\Controllers\SachController::class, 'show'])->name('products.show');
Route::get('/track-order', [App\Http\Controllers\CheckoutController::class, 'showTrackingSearch'])->name('tracking.search');
Route::post('/track-order', [App\Http\Controllers\CheckoutController::class, 'findOrder'])->name('tracking.find');
Route::get('/track-order/result/{id}', [App\Http\Controllers\CheckoutController::class, 'trackingResult'])->name('tracking.result');

// Các route yêu cầu đăng nhập + đã xác thực email
Route::middleware('verified')->group(function () {
    Route::get('/cart', [App\Http\Controllers\GioHangController::class, 'index'])->name('cart');
    Route::post('/cart/add', [App\Http\Controllers\GioHangController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [App\Http\Controllers\GioHangController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [App\Http\Controllers\GioHangController::class, 'destroy'])->name('cart.remove');
    Route::post('/cart/coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::get('/cart/coupons-available', [CouponController::class, 'availableForCart'])->name('cart.coupons.available');

    Route::get('/checkout', [App\Http\Controllers\GioHangController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\GioHangController::class, 'processCheckout'])->name('checkout.process');

    Route::get('/order-success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('order.success');

    // Theo dõi đơn hàng (Yêu cầu login)
    Route::get('/order-tracking/{id}', [App\Http\Controllers\CheckoutController::class, 'tracking'])->name('order.tracking');

    Route::get('/wishlist', [WishlistController::class, 'show'])->name('wishlist');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');


    Route::get('/profile', [UserController::class, 'show'])->name('profile');
    Route::get('/my-orders', [UserController::class, 'orders'])->name('my-orders');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('profile.password.update');

    // Đánh giá sách
    Route::post('/danh-gia', [DanhGiaController::class, 'store'])->name('danh-gia.store');
    Route::delete('/danh-gia/{id}', [DanhGiaController::class, 'destroy'])->name('danh-gia.destroy');
});


Route::get('/blog', function (\Illuminate\Http\Request $request) {
    // Lấy bài nổi bật: post có nhiều lượt xem nhất
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

Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:5000',
    ]);

    // Lưu hoặc gửi mail tại đây nếu cần
    // Mail::to('support@modtrabooks.vn')->send(new ContactMail($request->all()));

    return redirect()->route('contact')->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
})->name('contact.send');

// ─── Chatbot AI ──────────────────────────────────────────────────────────
Route::get('/chat/messege', [ChatbotController::class, 'fetchMessage'])->name('chatbot.fetchMessage');
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');


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

    // Quản lý đơn hàng
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

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
    Route::get('/customers', [UserController::class, 'customers'])->name('admin.customers.index');
    Route::get('/admins', [UserController::class, 'admins'])->name('admin.admins.index');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Quản lý Blog
    Route::get('/blogs', [\App\Http\Controllers\PostController::class, 'adminIndex'])->name('admin.blogs.index');
    Route::put('/blogs/{post}/approve', [\App\Http\Controllers\PostController::class, 'approve'])->name('admin.blogs.approve');
    Route::put('/blogs/{post}/reject', [\App\Http\Controllers\PostController::class, 'reject'])->name('admin.blogs.reject');

    // Quản lý mã giảm giá (Coupon)
    Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');
    Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store');
    Route::put('/coupons/{id}/toggle', [CouponController::class, 'toggleStatus'])->name('admin.coupons.toggle');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('admin.coupons.destroy');
    Route::get('/coupons/export-csv', [CouponController::class, 'exportCsv'])->name('admin.coupons.export');
    Route::post('/coupons/import-csv', [CouponController::class, 'importCsv'])->name('admin.coupons.import');

    // Quản lý Banner
    Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('admin.banners.store');
    Route::put('/banners/{id}', [BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');
    Route::put('/banners/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('admin.banners.toggle');
    
    // Cài đặt hệ thống
    Route::get('/settings', function() {
        return view('admin.settings');
    })->name('admin.settings');
});



// ─── Blog Management (User) ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/blog/create', [\App\Http\Controllers\PostController::class, 'create'])->name('blog.create');
    Route::post('/blog/store', [\App\Http\Controllers\PostController::class, 'store'])->name('blog.store');
    Route::post('/blog/upload-image', [\App\Http\Controllers\PostController::class, 'uploadImage'])->name('blog.upload-image');
});

// Chú ý: Route chứa tham số động {slug} phải nằm dưới các route tĩnh (như /blog/create)
Route::get('/blog/{slug}', function ($slug) {
    $post = \App\Models\Post::where('slug', $slug)
                ->where('status', 'published')
                ->with('user')
                ->firstOrFail();

    // Tăng lượt xem mỗi lần có người vào đọc
    $post->increment('views');

    // Bài viết liên quan: cùng category, loại trừ bài hiện tại, nhiều view trước
    $relatedPosts = \App\Models\Post::where('status', 'published')
                        ->where('category', $post->category)
                        ->where('id', '!=', $post->id)
                        ->with('user')
                        ->orderBy('views', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();

    // Nếu chưa đủ 3, bổ sung bài mới nhất từ category khác
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

// Live search AJAX
Route::get('/api/search', [App\Http\Controllers\SachController::class, 'searchAjax'])->name('api.search');
