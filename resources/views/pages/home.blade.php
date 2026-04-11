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

    {{-- Sách Bán Chạy — Bảng xếp hạng theo thể loại --}}
    <section class="section" style="background: var(--color-white);" id="bestsellers">
        <div class="container">
            <div class="section-header" style="margin-bottom: var(--space-4);">
                <h2>🔥 Sách bán chạy</h2>
            </div>

            @php
                $useCategories = !empty($rankingByCategory);
                $panelsData = [];
                if ($useCategories) {
                    $panelsData = $rankingByCategory;
                } else {
                    $panelsData = [
                        ['id' => 0, 'name' => 'Tất cả', 'books' => $sachBanChay]
                    ];
                }
            @endphp

            @if(!$useCategories && $sachBanChay->isEmpty())
                <p style="color:var(--color-text-muted);">Chưa có dữ liệu sách bán chạy.</p>
            @else

            {{-- Category Tabs (chỉ hiện nếu có data theo thể loại) --}}
            @if($useCategories)
            <div class="hbs-tabs" id="hbs-tabs">
                @foreach($rankingByCategory as $ci => $cat)
                <button class="hbs-tab {{ $ci === 0 ? 'hbs-tab-active' : '' }}"
                        onclick="hbsSwitchCat({{ $cat['id'] }}, this)">
                    {{ $cat['name'] }}
                </button>
                @endforeach
            </div>
            @endif

            {{-- Panel cho mỗi thể loại (hoặc 1 panel mặc định nếu không có rank theo categories) --}}
            @foreach($panelsData as $ci => $cat)
            @php $catBooks = collect($cat['books']); $firstBook = $catBooks->first(); @endphp
            <div class="hbs-cat-panel" id="hbs-panel-{{ $cat['id'] }}" style="{{ $ci !== 0 ? 'display:none' : '' }}">

                <div class="hbs-layout">
                    {{-- LEFT: Danh sách xếp hạng --}}
                    <div class="hbs-left">
                        @foreach($catBooks->take(8) as $idx => $sach)
                        @php
                            $imgUrl  = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/'.$sach->file_anh_bia) : 'https://placehold.co/80x110?text=No');
                            $rankNum = $idx + 1;
                            $rColor  = $rankNum === 1 ? '#f59e0b' : ($rankNum === 2 ? '#94a3b8' : ($rankNum === 3 ? '#cd7c3a' : '#a0aec0'));
                            $isExtra = $rankNum > 5;
                            $catId   = $useCategories ? $cat['id'] : 0;
                        @endphp
                        <div class="hbs-row {{ $isExtra ? 'hbs-extra' : '' }} {{ $idx === 0 ? 'hbs-active' : '' }}"
                             data-idx="{{ $idx }}"
                             data-cat="{{ $catId }}"
                             onclick="hbsSelect(this, {{ $idx }}, {{ $catId }})"
                             style="{{ $isExtra ? 'display:none;' : '' }}">
                            <div class="hbs-rank" style="color:{{ $rColor }};">
                                <span style="font-size:20px; font-weight:900;">{{ str_pad($rankNum, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="material-icons" style="font-size:14px; color:{{ $rankNum <= 3 ? '#f59e0b' : '#10b981' }};">
                                    {{ $rankNum <= 3 ? 'trending_up' : 'arrow_upward' }}
                                </span>
                            </div>
                            <div class="hbs-thumb"><img src="{{ $imgUrl }}" alt="{{ $sach->tieu_de }}"></div>
                            <div class="hbs-info">
                                <div class="hbs-title">{{ Str::limit($sach->tieu_de, 55) }}</div>
                                <div class="hbs-author">{{ $sach->tacGia->ten_tac_gia ?? 'Chưa cập nhật' }}</div>
                                <div class="hbs-score">
                                    <span class="material-icons" style="font-size:13px;">local_fire_department</span>
                                    {{ number_format($sach->tong_ban ?? 0) }} đã bán
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($catBooks->count() > 5)
                        <button class="hbs-more-btn" onclick="hbsShowMore({{ $catId ?? 0 }}, this)">
                            <span class="material-icons" style="font-size:16px;">expand_more</span>
                            Xem thêm
                        </button>
                        @endif
                    </div>

                    {{-- RIGHT: Chi tiết sách đầu tiên --}}
                    @php
                        $fImg  = $firstBook->link_anh_bia ?: ($firstBook->file_anh_bia ? asset('uploads/books/'.$firstBook->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                        $fGia  = (float)$firstBook->gia_ban;
                        $fGiam = null;
                        if ($activeCoupons) {
                            $fGiam = $activeCoupons->loai === 'percent'
                                ? $fGia * (1 - $activeCoupons->gia_tri / 100)
                                : max(0, $fGia - $activeCoupons->gia_tri);
                            $fGiam = round($fGiam);
                        }
                        $fPct = ($fGiam && $fGiam < $fGia)
                            ? round((1 - $fGiam / $fGia) * 100)
                            : ($firstBook->gia_goc > $fGia ? round(($firstBook->gia_goc - $fGia) / $firstBook->gia_goc * 100) : 0);
                    @endphp
                    <div class="hbs-right" id="hbs-detail-{{ $catId ?? 0 }}">
                        <div class="hbs-detail-inner">
                            <div class="hbs-cover-wrap">
                                <img src="{{ $fImg }}" alt="{{ $firstBook->tieu_de }}" class="hbs-cover-img">
                                @if($fPct > 0)<span class="hbs-disc-badge">-{{ $fPct }}%</span>@endif
                            </div>
                            <div class="hbs-detail-body">
                                <a href="{{ route('products.show', $firstBook->id) }}" class="hbs-detail-title">{{ $firstBook->tieu_de }}</a>
                                <div class="hbs-detail-meta">Tác giả: <strong>{{ $firstBook->tacGia->ten_tac_gia ?? 'Chưa cập nhật' }}</strong></div>
                                @if($firstBook->nhaXuatBan)
                                <div class="hbs-detail-meta">NXB: <strong>{{ $firstBook->nhaXuatBan->ten_nxb }}</strong></div>
                                @endif
                                <div class="hbs-price-row">
                                    @if($fGiam && $fGiam < $fGia)
                                        <span class="hbs-price-main">{{ number_format($fGiam, 0, ',', '.') }} đ</span>
                                        <span class="hbs-price-old">{{ number_format($fGia, 0, ',', '.') }} đ</span>
                                        <span class="hbs-disc-tag">-{{ $fPct }}%</span>
                                    @else
                                        <span class="hbs-price-main">{{ number_format($fGia, 0, ',', '.') }} đ</span>
                                        @if($firstBook->gia_goc > $fGia)
                                        <span class="hbs-price-old">{{ number_format($firstBook->gia_goc, 0, ',', '.') }} đ</span>
                                        @endif
                                    @endif
                                </div>
                                @if($firstBook->mo_ta)
                                <div class="hbs-desc">{{ Str::limit(strip_tags($firstBook->mo_ta), 220) }}</div>
                                @endif
                                <div style="display:flex; gap:10px; margin-top:18px; flex-wrap:wrap;">
                                    <a href="{{ route('products.show', $firstBook->id) }}" class="btn btn-primary" style="flex:1; min-width:110px; text-align:center; font-size:13px;">Xem chi tiết</a>
                                    <form action="{{ route('cart.add') }}" method="POST" style="flex:1; min-width:110px;">
                                        @csrf
                                        <input type="hidden" name="sach_id" value="{{ $firstBook->id }}">
                                        <input type="hidden" name="so_luong" value="1">
                                        <button type="submit" class="btn btn-outline" style="width:100%; font-size:13px;">
                                            <span class="material-icons" style="font-size:15px;">shopping_cart</span> Thêm giỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>{{-- .hbs-layout --}}

                {{-- JSON data cho panel này --}}
                <script id="hbs-data-{{ $catId ?? 0 }}" type="application/json">
                [
                @foreach($catBooks->take(8) as $s)
                @php
                    $sImg  = $s->link_anh_bia ?: ($s->file_anh_bia ? asset('uploads/books/'.$s->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                    $sGia  = (float)$s->gia_ban;
                    $sGiam = null;
                    if ($activeCoupons) {
                        $sGiam = $activeCoupons->loai === 'percent'
                            ? $sGia * (1 - $activeCoupons->gia_tri / 100)
                            : max(0, $sGia - $activeCoupons->gia_tri);
                        $sGiam = round($sGiam);
                    }
                    $sPct = ($sGiam && $sGiam < $sGia)
                        ? round((1 - $sGiam / $sGia) * 100)
                        : ($s->gia_goc > $sGia ? round(($s->gia_goc - $sGia) / $s->gia_goc * 100) : 0);
                @endphp
                {
                    "id": {{ $s->id }},
                    "tieu_de": {{ json_encode($s->tieu_de) }},
                    "tac_gia": {{ json_encode($s->tacGia->ten_tac_gia ?? 'Chưa cập nhật') }},
                    "nxb": {{ json_encode($s->nhaXuatBan->ten_nxb ?? '') }},
                    "img": {{ json_encode($sImg) }},
                    "gia_ban": {{ $sGia }},
                    "gia_sau_giam": {{ $sGiam ?? 'null' }},
                    "gia_goc": {{ (float)($s->gia_goc ?? 0) }},
                    "pct_off": {{ $sPct }},
                    "mo_ta": {{ json_encode(Str::limit(strip_tags($s->mo_ta ?? ''), 220)) }},
                    "url": {{ json_encode(route('products.show', $s->id)) }},
                    "cart_url": {{ json_encode(route('cart.add')) }},
                    "tong_ban": {{ $s->tong_ban ?? 0 }}
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
                ]
                </script>

            </div>{{-- .hbs-cat-panel --}}
            @endforeach

            @endif
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

<style>
/* ── Homepage Bestseller Ranking Widget ───────────────────────────────── */
.hbs-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}
.hbs-tab {
    padding: 8px 16px;
    background: var(--color-white);
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    color: var(--color-text-secondary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
.hbs-tab:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
}
.hbs-tab.hbs-tab-active {
    background: var(--color-primary);
    color: var(--color-white);
    border-color: var(--color-primary);
    font-weight: 600;
}

.hbs-layout {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) { .hbs-layout { grid-template-columns: 1fr; } }

/* Left list rows */
.hbs-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    cursor: pointer;
    border-radius: 10px;
    border: 1.5px solid transparent;
    transition: background .18s, border-color .18s, transform .15s;
}
.hbs-row:hover { background: var(--color-bg-alt, #f7f7f7); transform: translateX(3px); }
.hbs-row.hbs-active { background: #fff5f5; border-color: var(--color-primary, #e53e3e); }
.hbs-rank { display: flex; flex-direction: column; align-items: center; width: 34px; flex-shrink: 0; line-height: 1; }
.hbs-thumb { width: 54px; height: 76px; border-radius: 6px; overflow: hidden; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,.12); }
.hbs-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.hbs-info { flex: 1; min-width: 0; }
.hbs-title { font-size: 14px; font-weight: 700; color: #1a202c; display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; line-height:1.35; }
.hbs-author { font-size: 12px; color: #718096; margin-top: 2px; }
.hbs-score { font-size: 12px; color: var(--color-primary, #e53e3e); font-weight: 600; margin-top: 3px; display:flex; align-items:center; gap:2px; }

.hbs-more-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%; margin-top: 12px; padding: 10px;
    border: 1.5px solid var(--color-primary, #e53e3e);
    border-radius: 10px; background: none;
    color: var(--color-primary, #e53e3e); font-weight: 700; font-size: 14px;
    cursor: pointer; transition: background .18s, color .18s;
}
.hbs-more-btn:hover { background: var(--color-primary, #e53e3e); color: white; }

/* Right detail panel */
.hbs-right {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.07);
    transition: opacity .2s;
    position: sticky;
    top: 80px;
}
.hbs-detail-inner { display: flex; }
.hbs-cover-wrap { position: relative; flex-shrink: 0; width: 180px; min-height: 260px; overflow: hidden; }
.hbs-cover-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.hbs-disc-badge { position: absolute; top: 10px; left: 10px; background: var(--color-primary, #e53e3e); color: white; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 20px; }
.hbs-detail-body { flex: 1; padding: 18px 20px; min-width: 0; }
.hbs-detail-title { font-size: 17px; font-weight: 800; color: #1a202c; line-height: 1.4; text-decoration: none; display: block; margin-bottom: 8px; }
.hbs-detail-title:hover { color: var(--color-primary, #e53e3e); }
.hbs-detail-meta { font-size: 13px; color: #718096; margin-bottom: 3px; }
.hbs-price-row { display: flex; align-items: baseline; gap: 8px; margin: 12px 0; flex-wrap: wrap; }
.hbs-price-main { font-size: 22px; font-weight: 800; color: var(--color-primary, #e53e3e); }
.hbs-price-old { font-size: 13px; color: #a0aec0; text-decoration: line-through; }
.hbs-disc-tag { font-size: 11px; background: #fef3c7; color: #d97706; font-weight: 700; padding: 2px 7px; border-radius: 20px; }
.hbs-desc { font-size: 13px; color: #718096; line-height: 1.6; margin-top: 6px; display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden; }
@media (max-width: 768px) {
    .hbs-detail-inner { flex-direction: column; }
    .hbs-cover-wrap { width: 100%; height: 200px; }
    .hbs-right { position: static; }
}
</style>

<script>
    var HBS_CSRF = '{{ csrf_token() }}';

    function hbsSwitchCat(catId, btn) {
        // Tab styling
        document.querySelectorAll('.hbs-tab').forEach(t => t.classList.remove('hbs-tab-active'));
        btn.classList.add('hbs-tab-active');
        // Hide all panels, show selected
        document.querySelectorAll('.hbs-cat-panel').forEach(p => p.style.display = 'none');
        var targetPanel = document.getElementById('hbs-panel-' + catId);
        if(targetPanel) targetPanel.style.display = '';
    }

    window.hbsSelect = function(row, idx, catId) {
        var panelElement = row.closest('.hbs-cat-panel');

        // Highlight row
        panelElement.querySelectorAll('.hbs-row').forEach(r => r.classList.remove('hbs-active'));
        row.classList.add('hbs-active');

        // Get data array
        var dataScript = document.getElementById('hbs-data-' + catId);
        if (!dataScript) return;
        var data = JSON.parse(dataScript.textContent);
        if (!data || !data[idx]) return;

        hbsRender(data[idx], catId);
    };

    window.hbsShowMore = function(catId, btn) {
        var panelElement = btn.closest('.hbs-cat-panel');
        panelElement.querySelectorAll('.hbs-extra').forEach(el => el.style.display = 'flex');
        btn.style.display = 'none';
    };

    function hbsRender(b, catId) {
        var panelId = 'hbs-detail-' + catId;
        var panel = document.getElementById(panelId);
        if (!panel) return;

        var priceHtml = (b.gia_sau_giam && b.gia_sau_giam < b.gia_ban)
            ? '<span class="hbs-price-main">' + fmt(b.gia_sau_giam) + ' đ</span><span class="hbs-price-old">' + fmt(b.gia_ban) + ' đ</span><span class="hbs-disc-tag">-' + b.pct_off + '%</span>'
            : '<span class="hbs-price-main">' + fmt(b.gia_ban) + ' đ</span>' + (b.gia_goc > b.gia_ban ? '<span class="hbs-price-old">' + fmt(b.gia_goc) + ' đ</span>' : '');
        var discBadge = b.pct_off > 0 ? '<span class="hbs-disc-badge">-' + b.pct_off + '%</span>' : '';
        var nxbHtml   = b.nxb ? '<div class="hbs-detail-meta">NXB: <strong>' + esc(b.nxb) + '</strong></div>' : '';
        var descHtml  = b.mo_ta ? '<div class="hbs-desc">' + esc(b.mo_ta) + '</div>' : '';
        panel.style.opacity = '0';
        setTimeout(function() {
            panel.innerHTML =
                '<div class="hbs-detail-inner">'
                + '<div class="hbs-cover-wrap"><img src="' + b.img + '" class="hbs-cover-img" alt="">' + discBadge + '</div>'
                + '<div class="hbs-detail-body">'
                + '<a href="' + b.url + '" class="hbs-detail-title">' + esc(b.tieu_de) + '</a>'
                + '<div class="hbs-detail-meta">Tác giả: <strong>' + esc(b.tac_gia) + '</strong></div>'
                + nxbHtml
                + '<div class="hbs-price-row">' + priceHtml + '</div>'
                + descHtml
                + '<div style="display:flex;gap:10px;margin-top:18px;flex-wrap:wrap;">'
                + '<a href="' + b.url + '" class="btn btn-primary" style="flex:1;min-width:110px;text-align:center;font-size:13px;">Xem chi tiết</a>'
                + '<form action="' + b.cart_url + '" method="POST" style="flex:1;min-width:110px;">'
                + '<input type="hidden" name="_token" value="' + HBS_CSRF + '">'
                + '<input type="hidden" name="sach_id" value="' + b.id + '">'
                + '<input type="hidden" name="so_luong" value="1">'
                + '<button type="submit" class="btn btn-outline" style="width:100%;font-size:13px;"><span class="material-icons" style="font-size:15px;">shopping_cart</span> Thêm giỏ</button>'
                + '</form></div>'
                + '</div></div>';
            panel.style.opacity = '1';
        }, 150);
    }

    function fmt(n) { return new Intl.NumberFormat('vi-VN').format(n); }
    function esc(s) { var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
</script>
@endsection
