@extends('layouts.app')

@section('title', 'Trang chủ')
@section('meta_description', 'Bookverse — Nhà sách trực tuyến hàng đầu. Khám phá hàng ngàn đầu sách chất lượng.')

@section('content')
    {{-- Hero Section --}}
    <section class="hero" id="hero">
        {{-- Sparkle particles --}}
        <div class="section-sparkles">
            <svg class="section-sparkle" style="top: 12%; left: 8%; animation-delay: 0s;" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            <svg class="section-sparkle" style="top: 25%; right: 12%; animation-delay: 0.8s;" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            <svg class="section-sparkle" style="bottom: 18%; left: 22%; animation-delay: 1.5s;" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            <svg class="section-sparkle" style="top: 45%; left: 38%; animation-delay: 2.2s;" viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            <svg class="section-sparkle" style="bottom: 30%; right: 28%; animation-delay: 1s;" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
        </div>

        {{-- Floating decoration shapes --}}
        <div class="floating-shapes">
            <div class="floating-shape" style="width: 120px; height: 120px; background: rgba(255,180,100,0.08); top: 10%; left: 3%; animation: floatDrift1 14s ease-in-out infinite;"></div>
            <div class="floating-shape" style="width: 180px; height: 180px; background: rgba(198,40,40,0.035); top: 50%; left: 8%; animation: floatDrift2 18s ease-in-out infinite 2s;"></div>
            <div class="floating-shape" style="width: 70px; height: 70px; background: rgba(255,160,80,0.1); top: 20%; right: 6%; animation: floatDrift3 12s ease-in-out infinite 1s;"></div>
            <div class="floating-shape" style="width: 150px; height: 150px; background: rgba(255,200,130,0.06); bottom: 15%; right: 10%; animation: floatDrift1 20s ease-in-out infinite 3s;"></div>
            <div class="floating-shape" style="width: 55px; height: 55px; background: rgba(198,40,40,0.05); top: 8%; right: 30%; animation: floatDrift2 11s ease-in-out infinite 5s;"></div>
            <div class="floating-shape" style="width: 90px; height: 90px; background: rgba(255,180,100,0.07); bottom: 25%; left: 35%; animation: floatDrift3 22s ease-in-out infinite 1.5s;"></div>
            {{-- Extra warm blobs --}}
            <div class="floating-shape" style="width: 200px; height: 200px; background: rgba(255,200,120,0.05); top: 30%; left: 45%; border-radius: 45% 55% 50% 50%; animation: floatDrift1 25s ease-in-out infinite 4s;"></div>
            <div class="floating-shape" style="width: 100px; height: 100px; background: rgba(198,40,40,0.03); bottom: 10%; left: 15%; border-radius: 40% 60% 55% 45%; animation: floatDrift3 16s ease-in-out infinite 2.5s;"></div>
        </div>

        <div class="container">
            <div class="hero-content">
                <span class="hero-label">Chào mừng đến với Bookverse</span>
                <h1>Khám phá & Đọc <span class="highlight">hàng ngàn</span> cuốn sách hay</h1>
                <p>Nhà sách trực tuyến hàng đầu với đa dạng thể loại từ các tác giả nổi tiếng trong và ngoài nước. Giao hàng nhanh, giá tốt nhất.</p>
                <form class="hero-search" action="{{ route('products.index') }}" method="GET">
                    <select name="category">
                        <option value="">Tất cả thể loại</option>
                        @foreach ($theLoais as $cat)
                            <option value="{{ $cat->ten_the_loai }}">{{ $cat->ten_the_loai }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="search" placeholder="Tìm kiếm sách, tác giả...">
                    <button type="submit">
                        <span class="material-icons" style="font-size: 18px;">search</span>
                        Tìm kiếm
                    </button>
                </form>
            </div>
            <div class="hero-books">
                @foreach($sachNoiBat->take(5) as $sach)
                @php
                    $imgUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                @endphp
                <a href="{{ route('products.show', $sach->id) }}" class="hero-book">
                    <img src="{{ $imgUrl }}" alt="{{ $sach->tieu_de }}">
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Wave Divider: Hero → Features --}}
    <div class="wave-divider">
        <svg viewBox="0 0 1200 50" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,0 C200,50 400,10 600,35 C800,60 1000,15 1200,30 L1200,50 L0,50 Z" fill="#FFF8F0"/>
        </svg>
    </div>

    {{-- Features Row --}}
    <section class="features-row" id="features">
        <div class="feature-item" style="--accent: #C62828;">
            <div class="feature-icon feature-icon-gradient">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    <path d="M8 7h8M8 11h5"/>
                </svg>
            </div>
            <div class="feature-title">Kho sách lớn</div>
        </div>
        <div class="feature-item" style="--accent: #FF8F00;">
            <div class="feature-icon feature-icon-gradient">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div class="feature-title">Giá tốt nhất</div>
        </div>
        <div class="feature-item" style="--accent: #2563EB;">
            <div class="feature-icon feature-icon-gradient">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <path d="M10 17h4V5H2v12h3"/>
                    <path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/>
                    <path d="M14 17h1"/>
                    <circle cx="7.5" cy="17.5" r="2.5"/>
                    <circle cx="17.5" cy="17.5" r="2.5"/>
                </svg>
            </div>
            <div class="feature-title">Giao hàng nhanh</div>
        </div>
        <div class="feature-item" style="--accent: #7C3AED;">
            <div class="feature-icon feature-icon-gradient">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <circle cx="12" cy="8" r="7"/>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
                </svg>
            </div>
            <div class="feature-title">Chính hãng</div>
        </div>
        <div class="feature-item" style="--accent: #059669;">
            <div class="feature-icon feature-icon-gradient">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div class="feature-title">An toàn</div>
        </div>
    </section>

    {{-- Bookshelf: Categories organized by shelves --}}
    @php
        // Build shelves: take up to 6 top categories, fetch their top books (max 7 each)
        $shelfCategories = $theLoais->take(6);
        $shelves = [];
        foreach ($shelfCategories as $cat) {
            $childIds = $cat->children ? $cat->children->pluck('id')->push($cat->id)->toArray() : [$cat->id];
            $books = \App\Models\Sach::with('tacGia')
                ->whereIn('the_loai_id', $childIds)
                ->orderByDesc('created_at')
                ->limit(7)
                ->get();
            $shelves[] = ['cat' => $cat, 'books' => $books];
        }
        // Spine color palette (reuse for book spines if no cover)
        $spineColors = ['#8B5A3C', '#2B4866', '#7A3B2E', '#3A5A3C', '#5D4A7C', '#8B6F43', '#C9A66B'];
    @endphp

    <section class="section shelf-section" id="categories">
        <div class="container">
            <div class="section-header shelf-header">
                <div>
                    <span class="section-label">Bộ sưu tập</span>
                    <h2>Tủ sách theo thể loại</h2>
                    <p class="shelf-subtitle">Chọn một kệ để khám phá những cuốn sách hay nhất</p>
                </div>
                <a href="{{ url('/products?view=categories') }}" class="shelf-header-link">
                    Xem toàn bộ thư viện
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="bookshelf-card">
                {{-- Tab Navigation --}}
                <div class="shelf-tabs" role="tablist" aria-label="Chọn thể loại sách">
                    @foreach ($shelves as $idx => $shelf)
                        <button type="button"
                                class="shelf-tab {{ $idx === 0 ? 'active' : '' }}"
                                data-shelf="shelf-panel-{{ $idx }}"
                                role="tab"
                                aria-selected="{{ $idx === 0 ? 'true' : 'false' }}"
                                aria-controls="shelf-panel-{{ $idx }}">
                            <span class="shelf-tab-dot"></span>
                            <span class="shelf-tab-label">{{ $shelf['cat']->ten_the_loai }}</span>
                            <span class="shelf-tab-count">{{ $shelf['books']->count() }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Shelf Panels --}}
                <div class="shelf-panels">
                    @foreach ($shelves as $idx => $shelf)
                        <div class="shelf-panel {{ $idx === 0 ? 'active' : '' }}"
                             id="shelf-panel-{{ $idx }}"
                             role="tabpanel"
                             aria-labelledby="shelf-tab-{{ $idx }}">

                            <div class="shelf-panel-header">
                                <div>
                                    <h3 class="shelf-title">{{ $shelf['cat']->ten_the_loai }}</h3>
                                    <p class="shelf-meta">{{ $shelf['books']->count() }} cuốn đang trên kệ</p>
                                </div>
                                <a href="{{ url('/products?category=' . urlencode($shelf['cat']->ten_the_loai)) }}"
                                   class="shelf-full-link">
                                    <span>Xem toàn bộ kệ</span>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>

                            @if ($shelf['books']->isEmpty())
                                <div class="shelf-empty">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" width="48" height="48">
                                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                                    </svg>
                                    <p>Kệ này đang trống, hãy quay lại sau nhé</p>
                                </div>
                            @else
                                <div class="shelf-books-row">
                                    @foreach ($shelf['books'] as $bIdx => $sach)
                                        @php
                                            $giaBan = (float)$sach->gia_ban;
                                            $imageUrl = $sach->link_anh_bia
                                                ?: ($sach->file_anh_bia
                                                    ? asset('uploads/books/' . $sach->file_anh_bia)
                                                    : null);
                                            $spineColor = $spineColors[$bIdx % count($spineColors)];
                                        @endphp
                                        <a href="{{ route('products.show', $sach->id) }}"
                                           class="shelf-book"
                                           style="--spine-color: {{ $spineColor }};"
                                           aria-label="{{ $sach->tieu_de }}">
                                            <div class="shelf-book-cover">
                                                @if ($imageUrl)
                                                    <img src="{{ $imageUrl }}"
                                                         alt="{{ $sach->tieu_de }}"
                                                         loading="lazy">
                                                @else
                                                    <div class="shelf-book-placeholder">
                                                        <span>{{ mb_substr($sach->tieu_de, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div class="shelf-book-gloss"></div>
                                            </div>
                                            <div class="shelf-book-info">
                                                <h4>{{ $sach->tieu_de }}</h4>
                                                <p class="shelf-book-author">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</p>
                                                <div class="shelf-book-price">{{ number_format($giaBan, 0, ',', '.') }}đ</div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                {{-- Wooden shelf plank --}}
                                <div class="shelf-plank" aria-hidden="true">
                                    <div class="shelf-plank-top"></div>
                                    <div class="shelf-plank-front"></div>
                                    <div class="shelf-plank-shadow"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Books --}}
    <section class="section section-frosted" id="featured-books">
        <div class="container">
            <div class="section-header">
                <div>
                    <span class="section-label">Sách nổi bật</span>
                    <h2>Bạn đang muốn đọc gì?</h2>
                </div>
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
                @endphp
                <div class="card" id="featured-book-{{ $sach->id }}">
                    <div style="position: relative;">
                        <a href="{{ route('products.show', $sach->id) }}" style="display:block;">
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 280px; object-fit: cover; display:block;">
                        </a>
                        @if($pctOff > 0)
                        <span class="badge badge-danger" style="position:absolute; top:var(--space-3); right:var(--space-3); z-index:5;">-{{ $pctOff }}%</span>
                        @endif
                        <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" style="position:absolute; bottom:var(--space-3); right:var(--space-3); z-index:5;">
                            @csrf
                            <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg);" title="Thêm vào giỏ">
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
                            <h3 class="card-title" style="min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $sach->tieu_de }}</h3>
                        </a>
                        <div class="card-subtitle" style="margin-bottom: var(--space-3);">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap:wrap; gap:4px;">
                            <div>
                                @if($giaSauGiam && $giaSauGiam < $giaBan)
                                    <span class="card-price" style="color: var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                    <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="card-price">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
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

    {{-- Sách Bán Chạy --}}
    <section class="section" id="bestsellers">
        <div class="container">
            <div class="section-header">
                <div>
                    <span class="section-label">Bán chạy nhất</span>
                    <h2>Sách bán chạy</h2>
                </div>
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
                    <div style="position: absolute; top: -10px; left: -10px; width: 40px; height: 40px; background: {{ $rankColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px; z-index: 10; box-shadow: var(--shadow-md);">
                        {{ $rank }}
                    </div>
                    <div style="position: relative;">
                        <a href="{{ route('products.show', $sach->id) }}" style="display:block;">
                            <img src="{{ $imageUrl }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 280px; object-fit: cover; display:block;">
                        </a>
                        @if($pctOff > 0)
                        <span class="badge badge-danger" style="position:absolute; top:var(--space-3); right:var(--space-3); z-index:5;">-{{ $pctOff }}%</span>
                        @endif
                        <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" style="position:absolute; bottom:var(--space-3); right:var(--space-3); z-index:5;">
                            @csrf
                            <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            <input type="hidden" name="so_luong" value="1">
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg);" title="Thêm vào giỏ">
                                <span class="material-icons">shopping_cart</span>
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="stars" style="color: #ffc107; font-size: 14px; margin-bottom: 5px;">
                            @php $avgStar = round($sach->trungBinhSao()); @endphp
                            @for ($s = 1; $s <= 5; $s++)
                                <span class="material-icons" style="font-size: 16px;">{{ $s <= $avgStar ? 'star' : ($s - $avgStar < 1 ? 'star_half' : 'star_outline') }}</span>
                            @endfor
                        </div>
                        <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                            <h3 class="card-title" style="min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $sach->tieu_de }}</h3>
                        </a>
                        <div class="card-subtitle" style="margin-bottom: var(--space-3);">{{ $sach->tacGia->ten_tac_gia ?? 'Đang cập nhật' }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap:wrap; gap:4px;">
                            <div>
                                @if($giaSauGiam && $giaSauGiam < $giaBan)
                                    <span class="card-price" style="color: var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                    <span style="font-size:12px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="card-price">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
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

    {{-- Organic Divider: Bestsellers → Why Choose Us --}}
    <div style="position: relative; height: 80px; overflow: hidden; margin-top: -1px;">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="position:absolute; bottom:0; width:100%; height:80px;" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="720" cy="80" rx="900" ry="60" fill="rgba(255,255,255,0.7)"/>
        </svg>
    </div>

    {{-- Why Choose Us --}}
    <section class="section section-frosted" id="why-choose-us">
        <div class="container">
            <div class="section-header" style="text-align: center; display: block; margin-bottom: var(--space-8);">
                <span class="section-label" style="display: inline-block;">Lý do chọn chúng tôi</span>
                <h2>Tại sao chọn Bookverse?</h2>
                <p style="color: var(--color-text-muted); margin-top: var(--space-3); max-width: 540px; margin-left: auto; margin-right: auto;">Chúng tôi cam kết mang đến trải nghiệm mua sách trực tuyến tốt nhất cho bạn</p>
            </div>
            <div class="why-grid">
                <div class="why-card" id="feature-shipping" style="--accent: #C62828; --accent-soft: rgba(198,40,40,0.1);">
                    <div class="why-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <path d="M10 17h4V5H2v12h3"/>
                            <path d="M20 17h2v-3.34a4 4 0 0 0-1.17-2.83L19 9h-5"/>
                            <path d="M14 17h1"/>
                            <circle cx="7.5" cy="17.5" r="2.5"/>
                            <circle cx="17.5" cy="17.5" r="2.5"/>
                        </svg>
                        <div class="why-icon-deco"></div>
                    </div>
                    <div class="card-title">Miễn phí vận chuyển</div>
                    <p>Miễn phí ship cho đơn hàng từ 300.000đ trên toàn quốc</p>
                </div>
                <div class="why-card" id="feature-returns" style="--accent: #059669; --accent-soft: rgba(5,150,105,0.1);">
                    <div class="why-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <polyline points="23 4 23 10 17 10"/>
                            <polyline points="1 20 1 14 7 14"/>
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                        </svg>
                        <div class="why-icon-deco"></div>
                    </div>
                    <div class="card-title">Đổi trả dễ dàng</div>
                    <p>Đổi trả miễn phí trong 7 ngày nếu sách bị lỗi</p>
                </div>
                <div class="why-card" id="feature-authentic" style="--accent: #F59E0B; --accent-soft: rgba(245,158,11,0.12);">
                    <div class="why-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <path d="m9 12 2 2 4-4"/>
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"/>
                            <path d="M22 4 12 14.01l-3-3"/>
                        </svg>
                        <div class="why-icon-deco"></div>
                    </div>
                    <div class="card-title">Sách chính hãng</div>
                    <p>100% sách chính hãng từ các nhà xuất bản uy tín</p>
                </div>
                <div class="why-card" id="feature-payment" style="--accent: #2563EB; --accent-soft: rgba(37,99,235,0.1);">
                    <div class="why-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <div class="why-icon-deco"></div>
                    </div>
                    <div class="card-title">Thanh toán an toàn</div>
                    <p>Bảo mật thông tin với nhiều phương thức thanh toán</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Bar --}}
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="stats-grid-bar">
                <div class="stat-item reveal">
                    <div class="stat-value">10,000+</div>
                    <div class="stat-label">Đầu sách đa dạng</div>
                </div>
                <div class="stat-item reveal">
                    <div class="stat-value">50,000+</div>
                    <div class="stat-label">Khách hàng tin tưởng</div>
                </div>
                <div class="stat-item reveal">
                    <div class="stat-value">99%</div>
                    <div class="stat-label">Khách hàng hài lòng</div>
                </div>
                <div class="stat-item reveal">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Hỗ trợ tận tâm</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="section section-warm" id="testimonials">
        {{-- Sparkles --}}
        <div class="section-sparkles">
            <svg class="section-sparkle" style="top: 15%; left: 10%;" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            <svg class="section-sparkle" style="top: 40%; right: 8%; animation-delay: 1s;" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            <svg class="section-sparkle" style="bottom: 20%; left: 45%; animation-delay: 2s;" viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
        </div>
        {{-- Decorative circles --}}
        <div class="deco-circle deco-circle-outline" style="width:120px; height:120px; top:30px; right:5%;"></div>
        <div class="deco-circle deco-circle-warm" style="width:60px; height:60px; bottom:40px; left:8%;"></div>
        <div class="container">
            <div class="section-header" style="text-align: center; display: block; margin-bottom: var(--space-8);">
                <span class="section-label" style="display: inline-block;">Đánh giá</span>
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
                    <p class="quote">"Bookverse là nơi tuyệt vời để tìm kiếm những cuốn sách hay. Giao hàng nhanh, đóng gói cẩn thận. Rất hài lòng!"</p>
                    <div class="author-info">
                        <div class="author-avatar">{{ chr(64 + $i) }}</div>
                        <div>
                            <div class="author-name">Độc giả {{ $i }}</div>
                            <div class="author-role">Thành viên Bookverse</div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>
@endsection
