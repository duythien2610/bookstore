@extends('layouts.app')

@section('title', 'Chi tiết sách')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <a href="{{ url('/products') }}">Tất cả sách</a>
                <span class="separator">›</span>
                <span>Chi tiết sách</span>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Product Detail --}}
        <div style="display: grid; grid-template-columns: 400px 1fr; gap: var(--space-12); margin-bottom: var(--space-16);" id="product-detail">
            {{-- Image --}}
            <div>
                <div style="background: var(--color-bg-alt); border-radius: var(--radius-xl); aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center;">
                    <span class="material-icons" style="font-size: 120px; color: var(--color-text-muted);">book</span>
                </div>
            </div>

            {{-- Info --}}
            <div>
                <span class="badge badge-primary" style="margin-bottom: var(--space-3);">Bán chạy</span>
                <h1 style="font-size: var(--font-size-3xl); margin-bottom: var(--space-2);" id="product-title">Tên sách mẫu</h1>
                <p style="font-size: var(--font-size-base); color: var(--color-text-secondary); margin-bottom: var(--space-4);">bởi <a href="#" style="font-weight: var(--font-semibold);">Tác giả</a></p>

                <div class="stars" style="margin-bottom: var(--space-4);">
                    <span class="material-icons">star</span>
                    <span class="material-icons">star</span>
                    <span class="material-icons">star</span>
                    <span class="material-icons">star</span>
                    <span class="material-icons empty">star</span>
                    <span style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-left: var(--space-2);">4.2 (128 đánh giá)</span>
                </div>

                <div style="display: flex; align-items: baseline; gap: var(--space-3); margin-bottom: var(--space-6);">
                    <span style="font-size: var(--font-size-3xl); font-weight: var(--font-bold); color: var(--color-primary-dark);">189.000đ</span>
                    <span style="font-size: var(--font-size-lg); color: var(--color-text-muted); text-decoration: line-through;">320.000đ</span>
                    <span class="badge badge-danger">-41%</span>
                </div>

                <p style="color: var(--color-text-secondary); line-height: 1.7; margin-bottom: var(--space-8);">
                    Đây là mô tả ngắn về cuốn sách. Cuốn sách mang đến những góc nhìn mới mẻ về cuộc sống, giúp bạn phát triển tư duy và khám phá bản thân. Phù hợp cho mọi lứa tuổi.
                </p>

                {{-- Quantity & Actions --}}
                <div style="display: flex; gap: var(--space-4); align-items: center; margin-bottom: var(--space-6);">
                    <div class="quantity-control">
                        <button type="button" id="btn-qty-minus">−</button>
                        <span id="qty-value">1</span>
                        <button type="button" id="btn-qty-plus">+</button>
                    </div>
                    <button class="btn btn-primary btn-lg" id="btn-add-to-cart" style="flex: 1;">
                        <span class="material-icons">shopping_cart</span>
                        Thêm vào giỏ hàng
                    </button>
                    <button class="btn btn-outline btn-lg" id="btn-add-wishlist">
                        <span class="material-icons">favorite_border</span>
                    </button>
                </div>

                {{-- Product Meta --}}
                <div style="background: var(--color-bg); border-radius: var(--radius-xl); padding: var(--space-6);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); font-size: var(--font-size-sm);">
                        <div><span style="color: var(--color-text-muted);">ISBN:</span> 978-604-xxx-xxx-x</div>
                        <div><span style="color: var(--color-text-muted);">Nhà xuất bản:</span> NXB Trẻ</div>
                        <div><span style="color: var(--color-text-muted);">Số trang:</span> 328</div>
                        <div><span style="color: var(--color-text-muted);">Ngôn ngữ:</span> Tiếng Việt</div>
                        <div><span style="color: var(--color-text-muted);">Bìa:</span> Bìa mềm</div>
                        <div><span style="color: var(--color-text-muted);">Kích thước:</span> 13 × 20.5 cm</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <section style="margin-bottom: var(--space-16);" id="reviews">
            <h2 style="margin-bottom: var(--space-6);">Đánh giá từ độc giả</h2>
            <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                @for ($i = 1; $i <= 3; $i++)
                <div class="testimonial">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-3);">
                        <div class="author-info">
                            <div class="author-avatar">{{ chr(64 + $i) }}</div>
                            <div>
                                <div class="author-name">Người đánh giá {{ $i }}</div>
                                <div class="author-role">{{ rand(1, 30) }} ngày trước</div>
                            </div>
                        </div>
                        <div class="stars">
                            @for ($s = 1; $s <= 5; $s++)
                                <span class="material-icons" style="font-size: 14px;">{{ $s <= (6 - $i) ? 'star' : 'star' }}</span>
                            @endfor
                        </div>
                    </div>
                    <p class="quote" style="font-style: normal;">"Cuốn sách rất hay và ý nghĩa. Nội dung dễ hiểu, trình bày đẹp. Rất đáng để đọc."</p>
                </div>
                @endfor
            </div>
        </section>

        {{-- Related Books --}}
        <section class="section" style="padding-top: 0;" id="related-books">
            <div class="section-header">
                <h2>Sách liên quan</h2>
                <a href="{{ url('/products') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            <div class="book-grid book-grid-4">
                @for ($i = 1; $i <= 4; $i++)
                <div class="card">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                    </div>
                    <div class="card-body">
                        <div class="card-title">Sách liên quan {{ $i }}</div>
                        <div class="card-subtitle">Tác giả {{ $i }}</div>
                        <div class="card-price">{{ number_format(rand(89, 199) * 1000, 0, ',', '.') }}đ</div>
                    </div>
                </div>
                @endfor
            </div>
        </section>
    </div>
@endsection
