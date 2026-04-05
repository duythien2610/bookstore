@extends('layouts.app')

@section('title', 'Trang chủ')
@section('meta_description', 'Modtra Books — Nhà sách trực tuyến hàng đầu. Khám phá hàng ngàn đầu sách chất lượng.')

@section('content')
    {{-- Hero Section --}}
    <section class="hero" id="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Một cuốn sách hay có thể thay đổi cả một ngày của bạn</h1>
                <p>Khám phá hàng ngàn đầu sách từ các tác giả nổi tiếng trong và ngoài nước. Giao hàng nhanh, giá tốt nhất.</p>
                <div style="display: flex; gap: var(--space-4);">
                    <a href="{{ url('/products') }}" class="btn btn-primary btn-lg" id="btn-explore">
                        <span class="material-icons">auto_stories</span>
                        Khám phá ngay
                    </a>
                    <a href="{{ url('/products?view=categories') }}" class="btn btn-outline btn-lg" id="btn-categories">Thể loại sách</a>
                </div>
            </div>
            <div class="hero-image">
                <div style="width: 360px; height: 400px; background: rgba(var(--color-primary-rgb), 0.15); border-radius: var(--radius-2xl); display: flex; align-items: center; justify-content: center;">
                    <span class="material-icons" style="font-size: 120px; color: var(--color-primary);">menu_book</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="section" id="categories">
        <div class="container">
            <div class="section-header">
                <h2>Thể loại phổ biến</h2>
                <a href="{{ url('/products?view=categories') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: var(--space-4);">
                @php
                    $categories = [
                        ['icon' => 'psychology', 'name' => 'Tâm lý'],
                        ['icon' => 'business', 'name' => 'Kinh doanh'],
                        ['icon' => 'science', 'name' => 'Khoa học'],
                        ['icon' => 'auto_stories', 'name' => 'Tiểu thuyết'],
                        ['icon' => 'child_care', 'name' => 'Thiếu nhi'],
                        ['icon' => 'school', 'name' => 'Giáo dục'],
                    ];
                @endphp
                @foreach ($categories as $cat)
                <a href="{{ url('/products?category=' . $cat['name']) }}" class="card" style="text-align: center; padding: var(--space-6); text-decoration: none;">
                    <span class="material-icons" style="font-size: 36px; color: var(--color-primary); margin-bottom: var(--space-3);">{{ $cat['icon'] }}</span>
                    <div class="card-title" style="font-size: var(--font-size-sm);">{{ $cat['name'] }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Bestsellers --}}
    <section class="section" style="background: var(--color-white);" id="bestsellers">
        <div class="container">
            <div class="section-header">
                <h2>Sách bán chạy</h2>
                <a href="{{ url('/products?sort=bestseller') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            <div class="book-grid book-grid-4">
                @for ($i = 1; $i <= 4; $i++)
                <div class="card" id="bestseller-{{ $i }}">
                    <div style="position: relative;">
                        <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                            <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                        </div>
                        <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: var(--space-3);">-{{ rand(10, 40) }}%</span>
                    </div>
                    <div class="card-body">
                        <div class="card-title">Sách bán chạy {{ $i }}</div>
                        <div class="card-subtitle">Tác giả {{ $i }}</div>
                        <div class="card-price">
                            {{ number_format(rand(89, 199) * 1000, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- Featured Books --}}
    <section class="section" id="featured-books">
        <div class="container">
            <div class="section-header">
                <h2>Sách nổi bật tuần này</h2>
                <a href="{{ url('/products') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            <div class="book-grid book-grid-4">
                @for ($i = 1; $i <= 4; $i++)
                <div class="card" id="featured-book-{{ $i }}">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                    </div>
                    <div class="card-body">
                        <div class="stars" style="margin-bottom: var(--space-2);">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons empty">star</span>
                        </div>
                        <div class="card-title">Tên sách mẫu {{ $i }}</div>
                        <div class="card-subtitle">Tác giả {{ $i }}</div>
                        <div class="card-price">
                            {{ number_format(rand(89, 299) * 1000, 0, ',', '.') }}đ
                            <span class="original">{{ number_format(rand(300, 450) * 1000, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="section" style="background: var(--color-white);" id="why-choose-us">
        <div class="container">
            <div class="section-header" style="text-align: center; display: block; margin-bottom: var(--space-8);">
                <h2>Tại sao chọn Modtra Books?</h2>
                <p style="color: var(--color-text-muted); margin-top: var(--space-3); max-width: 540px; margin-left: auto; margin-right: auto;">Chúng tôi cam kết mang đến trải nghiệm mua sách trực tuyến tốt nhất cho bạn</p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-6);">
                {{-- Free Shipping --}}
                <div class="card" id="feature-shipping" style="text-align: center; padding: var(--space-8) var(--space-5);">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(var(--color-primary-rgb), 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <span class="material-icons" style="font-size: 28px; color: var(--color-primary);">local_shipping</span>
                    </div>
                    <div class="card-title" style="margin-bottom: var(--space-2);">Miễn phí vận chuyển</div>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Miễn phí ship cho đơn hàng từ 300.000đ trên toàn quốc</p>
                </div>

                {{-- Easy Returns --}}
                <div class="card" id="feature-returns" style="text-align: center; padding: var(--space-8) var(--space-5);">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(76, 175, 80, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <span class="material-icons" style="font-size: 28px; color: #4caf50;">autorenew</span>
                    </div>
                    <div class="card-title" style="margin-bottom: var(--space-2);">Đổi trả dễ dàng</div>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Đổi trả miễn phí trong 7 ngày nếu sách bị lỗi</p>
                </div>

                {{-- Authentic Books --}}
                <div class="card" id="feature-authentic" style="text-align: center; padding: var(--space-8) var(--space-5);">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(255, 152, 0, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <span class="material-icons" style="font-size: 28px; color: #ff9800;">verified</span>
                    </div>
                    <div class="card-title" style="margin-bottom: var(--space-2);">Sách chính hãng</div>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">100% sách chính hãng từ các nhà xuất bản uy tín</p>
                </div>

                {{-- Secure Payment --}}
                <div class="card" id="feature-payment" style="text-align: center; padding: var(--space-8) var(--space-5);">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(33, 150, 243, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                        <span class="material-icons" style="font-size: 28px; color: #2196f3;">lock</span>
                    </div>
                    <div class="card-title" style="margin-bottom: var(--space-2);">Thanh toán an toàn</div>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Bảo mật thông tin với nhiều phương thức thanh toán</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2>Cảm nhận từ độc giả</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-6);">
                @for ($i = 1; $i <= 3; $i++)
                <div class="testimonial" id="testimonial-{{ $i }}">
                    <div class="stars" style="margin-bottom: var(--space-3);">
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                        <span class="material-icons">star</span>
                    </div>
                    <p class="quote">"Modtra Books là nơi tuyệt vời để tìm kiếm những cuốn sách hay. Giao hàng nhanh, đóng gói cẩn thận. Rất hài lòng!"</p>
                    <div class="author-info">
                        <div class="author-avatar">{{ chr(64 + $i) }}</div>
                        <div>
                            <div class="author-name">Độc giả {{ $i }}</div>
                            <div class="author-role">Thành viên Modtra</div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>
@endsection
