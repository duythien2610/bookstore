@extends('layouts.app')

@section('title', 'Trang chủ')
@section('meta_description', 'Modtra Books — Nhà sách trực tuyến hàng đầu. Khám phá hàng ngàn đầu sách chất lượng.')

@section('content')
    @push('styles')
    <style>
        .banner-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            cursor: pointer;
            border: 1px solid rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.5);
            transition: background 0.3s ease;
        }
        .banner-dot.active {
            background: white;
        }
    </style>
    @endpush
    {{-- Hero Section ngẫu nhiên hoặc carousel --}}
    @if(isset($banners) && $banners->isNotEmpty())
        <section class="hero" style="padding: 0; background: var(--color-bg); position: relative;" id="hero">
            <div style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; gap: 0; -ms-overflow-style: none; scrollbar-width: none;" id="banner-carousel">
                @foreach($banners as $banner)
                <div style="flex: 0 0 100%; min-width: 100%; scroll-snap-align: start; position: relative;">
                    @if($banner->anhSrc)
                        <img src="{{ $banner->anhSrc }}" alt="{{ $banner->tieu_de }}" style="width: 100%; height: 400px; object-fit: cover; display: block;">
                    @else
                        <div style="height: 400px; background: rgba(var(--color-primary-rgb), 0.15); display: flex; align-items: center; justify-content: center;">
                            <span class="material-icons" style="font-size: 100px; color: var(--color-primary);">menu_book</span>
                        </div>
                    @endif
                    
                    {{-- Overlay nếu có tiêu đề / mô tả --}}
                    @if($banner->tieu_de || $banner->mo_ta || $banner->lien_ket)
                    <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 100%); display: flex; align-items: center; z-index: 2;">
                        <div class="container">
                            <div style="max-width: 500px; color: white;">
                                @if($banner->tieu_de)
                                <h1 style="color: white; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5); margin-bottom: var(--space-4);">{{ $banner->tieu_de }}</h1>
                                @endif
                                @if($banner->mo_ta)
                                <p style="font-size: 1.1rem; text-shadow: 0 1px 3px rgba(0,0,0,0.5); margin-bottom: var(--space-4);">{{ $banner->mo_ta }}</p>
                                @endif
                                @if($banner->lien_ket)
                                <a href="{{ $banner->lien_ket }}" class="btn btn-primary btn-lg">Xem chi tiết</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @elseif($banner->lien_ket)
                        {{-- Toàn bộ hình là clickable --}}
                        <a href="{{ $banner->lien_ket }}" style="position: absolute; inset:0; z-index: 10;"></a>
                    @endif
                </div>
                @endforeach
            </div>

            @if($banners->count() > 1)
            <div style="position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 20;">
                @foreach($banners as $idx => $banner)
                <div class="banner-dot {{ $idx === 0 ? 'active' : '' }}" onclick="document.getElementById('banner-carousel').children[Number('{{$idx}}')].scrollIntoView()"></div>
                @endforeach
            </div>
            @endif
        </section>
    @else
        {{-- Static Hero Section (Fallback) --}}
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
    @endif


    {{-- Featured Books --}}
    <section class="section" id="featured-books">
        <div class="container">
            <div class="section-header">
                <h2>Sách nổi bật tuần này</h2>
                <a href="{{ url('/products') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            @if($sachMoi->isEmpty())
            <p style="color: var(--color-text-muted); text-align: center; padding: var(--space-8) 0;">Chưa có sách nào. Hãy thêm sách vào kho!</p>
            @else
            <div class="book-grid book-grid-4">
                @foreach ($sachMoi as $sach)
                <div class="card" id="featured-book-{{ $loop->index + 1 }}">
                    <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                        <div class="card-img" style="display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if ($sach->file_anh_bia)
                                <img src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @elseif ($sach->link_anh_bia)
                                <img src="{{ $sach->link_anh_bia }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                            @else
                                <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="card-title" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-clamp:2;">{{ $sach->tieu_de }}</div>
                            <div class="card-subtitle">{{ $sach->tacGia->ten_tac_gia ?? '' }}</div>
                            <div class="card-price">{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- Bestsellers --}}
    <section class="section" style="background: var(--color-white);" id="bestsellers">
        <div class="container">
            <div class="section-header">
                <h2>Sách khuyến mãi</h2>
                <a href="{{ url('/products?gia_goc=1') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            @if($sachBanChay->isEmpty())
            <p style="color: var(--color-text-muted); text-align: center; padding: var(--space-8) 0;">Chưa có sách khuyến mãi.</p>
            @else
            <div class="book-grid book-grid-4">
                @foreach ($sachBanChay as $sach)
                <div class="card" id="bestseller-{{ $loop->index + 1 }}">
                    <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                        <div style="position: relative;">
                            <div class="card-img" style="display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @if ($sach->file_anh_bia)
                                    <img src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @elseif ($sach->link_anh_bia)
                                    <img src="{{ $sach->link_anh_bia }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                                @else
                                    <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                                @endif
                            </div>
                            @if($sach->gia_goc > $sach->gia_ban)
                            <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: var(--space-3);">
                                -{{ round(($sach->gia_goc - $sach->gia_ban) / $sach->gia_goc * 100) }}%
                            </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="card-title" style="-webkit-line-clamp:2;-webkit-box-orient:vertical;display:-webkit-box;overflow:hidden;line-clamp:2;">{{ $sach->tieu_de }}</div>
                            <div class="card-subtitle">{{ $sach->tacGia->ten_tac_gia ?? '' }}</div>
                            <div class="card-price">{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="section" id="why-choose-us">
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
    <section class="section" style="background: var(--color-white);" id="testimonials">
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

    {{-- Categories --}}
    <section class="section" id="categories">
        <div class="container">
            <div class="section-header">
                <h2>Thể loại phổ biến</h2>
                <a href="{{ url('/products') }}">Xem tất cả <span class="material-icons" style="font-size: 16px;">arrow_forward</span></a>
            </div>
            @php
                $catIcons = ['psychology','business','science','auto_stories','child_care','school','menu_book','sports_esports'];
            @endphp
            <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: var(--space-4);">
                @forelse ($theLoais as $tl)
                <a href="{{ route('products.index', ['the_loai_id' => $tl->id]) }}" class="card" style="text-align: center; padding: var(--space-6); text-decoration: none;">
                    <span class="material-icons" style="font-size: 36px; color: var(--color-primary); margin-bottom: var(--space-3);">{{ $catIcons[$loop->index % count($catIcons)] }}</span>
                    <div class="card-title" style="font-size: var(--font-size-sm);">{{ $tl->ten_the_loai }}</div>
                </a>
                @empty
                <p style="color: var(--color-text-muted); grid-column: span 6; text-align: center;">Chưa có thể loại nào.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
