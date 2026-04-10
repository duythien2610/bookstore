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
                    $iconMap = [
                        'tâm lý'      => 'psychology',
                        'kỹ năng'     => 'psychology',
                        'kinh'        => 'business',
                        'business'    => 'business',
                        'management'  => 'business',
                        'khoa học'    => 'science',
                        'science'     => 'science',
                        'tiểu thuyết' => 'auto_stories',
                        'văn học'     => 'auto_stories',
                        'fiction'     => 'auto_stories',
                        'thiếu nhi'   => 'child_care',
                        'nuôi dạy'    => 'child_care',
                        'giáo dục'    => 'school',
                        'personal'    => 'self_improvement',
                        'development' => 'self_improvement',
                        'tiểu sử'     => 'person',
                        'hồi ký'      => 'person',
                    ];
                    function getCatIcon($name, $map) {
                        $lower = mb_strtolower($name);
                        foreach ($map as $keyword => $icon) {
                            if (str_contains($lower, $keyword)) return $icon;
                        }
                        return 'menu_book';
                    }
                @endphp
                @foreach ($theLoais as $cat)
                <a href="{{ url('/products?category=' . urlencode($cat->ten_the_loai)) }}" class="card" style="text-align: center; padding: var(--space-6); text-decoration: none;">
                    <span class="material-icons" style="font-size: 36px; color: var(--color-primary); margin-bottom: var(--space-3);">{{ getCatIcon($cat->ten_the_loai, $iconMap) }}</span>
                    <div class="card-title" style="font-size: var(--font-size-sm);">{{ $cat->ten_the_loai }}</div>
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
                @foreach($sachNoiBat->take(4) as $index => $sach)
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
                    $pctOff = ($giaSauGiam && $giaSauGiam < $giaBan)
                        ? round((1 - $giaSauGiam/$giaBan)*100)
                        : ($sach->gia_goc > $giaBan ? round(($sach->gia_goc - $giaBan)/$sach->gia_goc*100) : 0);
                    $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                    $rank = $index + 1;
                    $rankColor = $rank === 1 ? '#f59e0b' : ($rank === 2 ? '#94a3b8' : ($rank === 3 ? '#cd7c3a' : 'var(--color-primary)'));
                @endphp
                <div class="card" id="featured-book-{{ $sach->id }}" style="position: relative;">
                    {{-- Rank badge --}}
                    <div style="position: absolute; top: -10px; left: -10px; width: 40px; height: 40px; background: {{ $rankColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; z-index: 10; box-shadow: var(--shadow-md);">
                        {{ $rank }}
                    </div>

                    <div style="position: relative;">
                        <a href="{{ route('products.show', $sach->id) }}" style="display:block;">
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 280px; object-fit: cover; border-radius: var(--radius-md) var(--radius-md) 0 0; display:block;">
                        </a>

                        {{-- Discount badge --}}
                        @if($pctOff > 0)
                        <span class="badge badge-danger" style="position:absolute; top:var(--space-3); right:var(--space-3); z-index:5;">-{{ $pctOff }}%</span>
                        @endif



                        {{-- Add to cart --}}
                        <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" style="position:absolute; bottom:var(--space-3); right:var(--space-3); z-index:5;">
                            @csrf
                            <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg);" title="Thêm vào giỏ">
                                <span class="material-icons">shopping_cart</span>
                            </button>
                        </form>
                    </div>

                    <div class="card-body" style="padding: var(--space-4);">
                        <div class="stars" style="color: #ffc107; font-size: 14px; margin-bottom: 5px;">
                            @php $avgRating = $sach->trungBinhSao(); @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="material-icons" style="font-size: 16px;">{{ $i <= $avgRating ? 'star' : ($i - $avgRating < 1 ? 'star_half' : 'star_outline') }}</span>
                            @endfor
                        </div>
                        <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                            <h3 class="card-title" style="font-size: 16px; min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $sach->tieu_de }}</h3>
                        </a>
                        <div class="card-subtitle" style="margin-bottom: var(--space-3);">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap:wrap; gap:4px;">
                            <div>
                                @if($giaSauGiam && $giaSauGiam < $giaBan)
                                    <span class="card-price" style="font-size: 16px; font-weight: 700; color: var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                    <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="card-price" style="font-size: 16px; font-weight: 700;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                    @if($sach->gia_goc > $giaBan)
                                        <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($sach->gia_goc, 0, ',', '.') }}đ</span>
                                    @endif
                                @endif
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                                <span style="color: var(--color-success);">{{ $sach->tong_ban ?? 0 }}</span> đã bán
                            </div>
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
                @forelse($sachBanChay as $index => $sach)
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
                    $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                    $rank = $index + 1;
                    $rankColor = $rank === 1 ? '#f59e0b' : ($rank === 2 ? '#94a3b8' : ($rank === 3 ? '#cd7c3a' : 'var(--color-primary)'));
                @endphp
                <div class="card" id="bestseller-{{ $sach->id }}" style="position: relative;">
                    {{-- Rank badge --}}
                    <div style="position: absolute; top: -10px; left: -10px; width: 40px; height: 40px; background: {{ $rankColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; z-index: 10; box-shadow: var(--shadow-md);">
                        {{ $rank }}
                    </div>

                    <div style="position: relative;">
                        <a href="{{ route('products.show', $sach->id) }}" style="display:block;">
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 280px; object-fit: cover; border-radius: var(--radius-md) var(--radius-md) 0 0; display:block;">
                        </a>

                        {{-- Discount badge --}}
                        @if($pctOff > 0)
                        <span class="badge badge-danger" style="position:absolute; top:var(--space-3); right:var(--space-3); z-index:5;">-{{ $pctOff }}%</span>
                        @endif



                        {{-- Add to cart --}}
                        <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" style="position:absolute; bottom:var(--space-3); right:var(--space-3); z-index:5;">
                            @csrf
                            <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg);" title="Thêm vào giỏ">
                                <span class="material-icons">shopping_cart</span>
                            </button>
                        </form>
                    </div>

                    <div class="card-body" style="padding: var(--space-4);">
                        <div class="stars" style="color: #ffc107; font-size: 14px; margin-bottom: 5px;">
                            @php $avgStar = round($sach->trungBinhSao()); @endphp
                            @for ($s = 1; $s <= 5; $s++)
                                <span class="material-icons" style="font-size: 16px;">{{ $s <= $avgStar ? 'star' : ($s - $avgStar < 1 ? 'star_half' : 'star_outline') }}</span>
                            @endfor
                        </div>
                        <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                            <h3 class="card-title" style="font-size: 16px; min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $sach->tieu_de }}</h3>
                        </a>
                        <div class="card-subtitle" style="margin-bottom: var(--space-3);">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap:wrap; gap:4px;">
                            <div>
                                @if($giaSauGiam && $giaSauGiam < $giaBan)
                                    <span class="card-price" style="font-size: 16px; font-weight: 700; color: var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                    <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="card-price" style="font-size: 16px; font-weight: 700;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                    @if($sach->gia_goc > $giaBan)
                                        <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($sach->gia_goc, 0, ',', '.') }}đ</span>
                                    @endif
                                @endif
                            </div>
                            <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 500;">
                                <span style="color: var(--color-success);">{{ $sach->tong_ban ?? 0 }}</span> đã bán
                            </div>
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
