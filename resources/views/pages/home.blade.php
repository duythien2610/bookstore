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


    {{-- Featured Books --}}
    <section class="section" id="featured-books">
        <div class="container">
            <div class="section-header">
                <h2>Sách nổi bật</h2>
                <a href="{{ route('products.featured') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            @if($activeCoupons)
            <div style="display:flex; align-items:center; gap:var(--space-3); background:linear-gradient(135deg,#fff3cd,#ffe69c); border:1px solid #ffc107; border-radius:var(--radius-lg); padding:var(--space-3) var(--space-5); margin-bottom:var(--space-6);">
                <span class="material-icons" style="color:#856404; font-size:22px;">local_offer</span>
                <div>
                    <strong style="color:#856404;">Khuyến mãi đang diễn ra!</strong>
                    <span style="color:#856404; margin-left:8px;">Dùng mã <strong>{{ $activeCoupons->ma_code }}</strong> để giảm {{ $activeCoupons->loai === 'percent' ? $activeCoupons->gia_tri . '%' : number_format($activeCoupons->gia_tri, 0, ',', '.') . 'đ' }} cho đơn hàng của bạn!</span>
                </div>
            </div>
            @endif
            <div class="book-grid book-grid-4">
                @foreach($sachNoiBat->take(4) as $sach)
                @php
                    // Tính giá đã giảm nếu có mã khuyến mãi đang chạy
                    $giaBan = (float)$sach->gia_ban;
                    $giaSauGiam = null;
                    if ($activeCoupons) {
                        $giaSauGiam = $activeCoupons->loai === 'percent'
                            ? $giaBan * (1 - $activeCoupons->gia_tri / 100)
                            : max(0, $giaBan - $activeCoupons->gia_tri);
                        $giaSauGiam = round($giaSauGiam);
                    }
                @endphp
                <div class="card" id="featured-book-{{ $sach->id }}">
                    <div class="card-img" style="position:relative;">
                        @php
                            $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                        @endphp
                        <a href="{{ route('products.show', $sach->id) }}">
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md);">
                        </a>
                        @if($giaSauGiam && $giaSauGiam < $giaBan)
                            @php
                                $pctOff = round((1 - $giaSauGiam/$giaBan)*100);
                            @endphp
                            <span class="badge badge-danger" style="position:absolute; top:var(--space-3); left:var(--space-3);">-{{ $pctOff }}%</span>
                        @endif
                        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn btn-primary btn-sm" style="position: absolute; bottom: 10px; right: 10px; border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <span class="material-icons">shopping_cart</span>
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="stars" style="color: #ffc107; font-size: 14px; margin-bottom: 5px;">
                            @php $avgRating = $sach->trungBinhSao(); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="material-icons" style="font-size: 16px;">{{ $i <= $avgRating ? 'star' : ($i - $avgRating < 1 ? 'star_half' : 'star_outline') }}</span>
                            @endfor
                        </div>
                        <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                            <div class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $sach->tieu_de }}</div>
                        </a>
                        <div class="card-subtitle">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>
                        <div class="card-price">
                            @if($giaSauGiam && $giaSauGiam < $giaBan)
                                <span style="color:var(--color-danger); font-weight:700;">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                <span class="original" style="margin-left:6px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                            @else
                                {{ number_format($giaBan, 0, ',', '.') }}đ
                                @if($sach->gia_goc > $sach->gia_ban)
                                    <span class="original">{{ number_format($sach->gia_goc, 0, ',', '.') }}đ</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Sách Bán Chạy (dữ liệu thật từ đơn hàng thành công) --}}
    <section class="section" style="background: var(--color-white);" id="bestsellers">
        <div class="container">
            <div class="section-header">
                <h2>Sách bán chạy</h2>
                <a href="{{ route('products.bestselling') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            <div class="book-grid book-grid-4">
                @forelse($sachBanChay as $sach)
                @php
                    $giaBan = (float)$sach->gia_ban;
                    $giaSauGiam = null;
                    if ($activeCoupons) {
                        $giaSauGiam = $activeCoupons->loai === 'percent'
                            ? $giaBan * (1 - $activeCoupons->gia_tri / 100)
                            : max(0, $giaBan - $activeCoupons->gia_tri);
                        $giaSauGiam = round($giaSauGiam);
                    }
                    $pctOff = ($giaSauGiam && $giaSauGiam < $giaBan)
                        ? round((1 - $giaSauGiam/$giaBan)*100)
                        : ($sach->gia_goc > $giaBan ? round(($sach->gia_goc - $giaBan)/$sach->gia_goc*100) : 0);
                    $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : null);
                @endphp
                <div class="card" id="bestseller-{{ $sach->id }}">
                    <a href="{{ route('products.show', $sach->id) }}" style="display:block; position:relative;">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" class="card-img" style="display:block; width:100%; object-fit:cover;">
                        @else
                            <div class="card-img" style="display:flex; align-items:center; justify-content:center;">
                                <span class="material-icons" style="font-size:64px; color:var(--color-text-muted);">book</span>
                            </div>
                        @endif
                        {{-- Badge giảm giá --}}
                        @if($pctOff > 0)
                            <span class="badge badge-danger" style="position:absolute; top:var(--space-3); left:var(--space-3);">-{{ $pctOff }}%</span>
                        @endif
                        {{-- Badge lượt bán --}}
                        @if($sach->tong_ban > 0)
                            <span style="position:absolute; bottom:var(--space-3); left:var(--space-3); background:rgba(0,0,0,.55); color:#fff; font-size:11px; padding:2px 8px; border-radius:20px;">
                                🔥 {{ $sach->tong_ban }} đã bán
                            </span>
                        @endif
                    </a>
                    <div class="card-body">
                        <div class="stars" style="margin-bottom:var(--space-2);">
                            @php $avgStar = round($sach->trungBinhSao()); @endphp
                            @for ($s = 1; $s <= 5; $s++)
                                <span class="material-icons" style="font-size:14px; color:{{ $s <= $avgStar ? '#f59e0b' : '#e2e8f0' }};">star</span>
                            @endfor
                        </div>
                        <a href="{{ route('products.show', $sach->id) }}" class="card-title" style="display:block; color:var(--color-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $sach->tieu_de }}</a>
                        <div class="card-subtitle">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:var(--space-2);">
                            <div>
                                @if($giaSauGiam && $giaSauGiam < $giaBan)
                                    <span class="card-price" style="color:var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                    <span style="font-size:11px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="card-price">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                    @if($sach->gia_goc > $giaBan)
                                        <span style="font-size:11px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($sach->gia_goc, 0, ',', '.') }}đ</span>
                                    @endif
                                @endif
                            </div>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                                <input type="hidden" name="so_luong" value="1">
                                <button type="submit" style="background:var(--color-primary-light); border:none; border-radius:var(--radius-lg); width:36px; height:36px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Thêm vào giỏ">
                                    <span class="material-icons" style="font-size:18px; color:var(--color-primary-dark);">add_shopping_cart</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p style="color:var(--color-text-muted); grid-column:1/-1;">Chưa có dữ liệu sách bán chạy.</p>
                @endforelse
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
