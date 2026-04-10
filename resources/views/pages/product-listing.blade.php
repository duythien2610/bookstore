@extends('layouts.app')

@section('title', 'Danh sách sách')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>{{ request('view') == 'categories' ? 'Danh mục thể loại' : 'Tất cả sách' }}</span>
            </div>
            <h1>{{ request('view') == 'categories' ? 'Khám phá theo thể loại' : 'Danh sách sách' }}</h1>
        </div>
    </div>

    <div class="container">
        @php
            $isNewArrivals = request()->is('products') && !request()->hasAny(['category', 'search', 'rating', 'gia_min', 'gia_max', 'view']);
        @endphp
        <div class="listing-layout" style="{{ $isNewArrivals ? 'display: block;' : '' }}">
            {{-- Filter Sidebar --}}
            @if(!$isNewArrivals)
            <aside class="filter-sidebar" id="filter-sidebar">
                <form action="{{ route('products.index') }}" method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    
                    <div class="filter-group">
                        <div class="filter-group-header" onclick="this.parentElement.classList.toggle('collapsed')">
                            <h4>Thể loại</h4>
                            <span class="material-icons">expand_more</span>
                        </div>
                        <div class="filter-group-content">
                            <div class="category-scroll-container" style="max-height: 250px; overflow-y: auto; padding-right: 8px; margin-bottom: 15px; border: 1px solid var(--color-bg-alt); border-radius: 8px; padding: 10px;">
                                @foreach ($theLoais as $theLoai)
                                    <div class="category-item-wrapper" style="margin-bottom: var(--space-2);">
                                        <div class="form-check">
                                            <input type="checkbox" id="cat-{{ $theLoai->id }}" name="category[]" value="{{ $theLoai->ten_the_loai }}" 
                                                {{ in_array($theLoai->ten_the_loai, (array)request('category')) ? 'checked' : '' }}>
                                            <label for="cat-{{ $theLoai->id }}" style="font-weight: 600; font-size: 14px;">{{ $theLoai->ten_the_loai }}</label>
                                        </div>
                                        
                                        {{-- Danh mục con --}}
                                        @if($theLoai->children->count() > 0)
                                            <div class="sub-categories" style="margin-left: var(--space-4); margin-top: 4px; display: flex; flex-direction: column; gap: 4px; border-left: 2px solid var(--color-bg-alt); padding-left: 8px;">
                                                @foreach($theLoai->children as $child)
                                                    <div class="form-check">
                                                        <input type="checkbox" id="cat-{{ $child->id }}" name="category[]" value="{{ $child->ten_the_loai }}" 
                                                            {{ in_array($child->ten_the_loai, (array)request('category')) ? 'checked' : '' }}>
                                                        <label for="cat-{{ $child->id }}" style="font-size: 13px; color: var(--color-text-secondary);">{{ $child->ten_the_loai }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="filter-group-header" onclick="this.parentElement.classList.toggle('collapsed')">
                            <h4>Khoảng giá</h4>
                            <span class="material-icons">expand_more</span>
                        </div>
                        <div class="filter-group-content">
                            <div style="display: flex; gap: var(--space-2); align-items: center;">
                                <input type="number" name="gia_min" value="{{ request('gia_min') }}" class="form-control" placeholder="Từ" style="padding: var(--space-2);">
                                <span>—</span>
                                <input type="number" name="gia_max" value="{{ request('gia_max') }}" class="form-control" placeholder="Đến" style="padding: var(--space-2);">
                            </div>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="filter-group-header" onclick="this.parentElement.classList.toggle('collapsed')">
                            <h4>Đánh giá</h4>
                            <span class="material-icons">expand_more</span>
                        </div>
                        <div class="filter-group-content">
                            @for ($r = 5; $r >= 3; $r--)
                            <div class="form-check" style="margin-bottom: var(--space-2);">
                                <input type="radio" id="rating-{{ $r }}" name="rating" value="{{ $r }}" {{ request('rating') == $r ? 'checked' : '' }} class="rating-radio">
                                <label for="rating-{{ $r }}">
                                    <span class="stars" style="display: inline-flex;">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <span class="material-icons" style="font-size: 14px; color: {{ $s <= $r ? '#f59e0b' : '#e2e8f0' }};">star</span>
                                        @endfor
                                    </span>
                                    trở lên
                                </label>
                            </div>
                            @endfor
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" id="btn-apply-filter">Áp dụng bộ lọc</button>
                    @if(request()->hasAny(['category', 'gia_min', 'gia_max', 'rating', 'search']))
                        <a href="{{ route('products.index', request()->only('view')) }}" class="btn btn-outline btn-block" style="margin-top: 10px; text-align: center;">Xóa bộ lọc</a>
                    @endif
                </form>
            </aside>
            @endif

            {{-- Main Content AREA --}}
            <div style="flex: 1;">
                {{-- Product Grid View (Normal) --}}
                <div class="listing-topbar">
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">
                        Hiển thị <strong>{{ $sachs->firstItem() ?? 0 }}-{{ $sachs->lastItem() ?? 0 }}</strong> trong <strong>{{ $sachs->total() }}</strong> sách
                        @if($queryText) cho từ khóa "<strong>{{ $queryText }}</strong>"@endif
                    </p>
                    <select class="form-control" style="width: auto;" id="sort-select" onchange="location = this.value;">
                        <option value="{{ request()->fullUrlWithQuery(['sap_xep' => 'moi_nhat']) }}" {{ request('sap_xep') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ request()->fullUrlWithQuery(['sap_xep' => 'gia_tang']) }}" {{ request('sap_xep') == 'gia_tang' ? 'selected' : '' }}>Giá: Thấp → Cao</option>
                        <option value="{{ request()->fullUrlWithQuery(['sap_xep' => 'gia_giam']) }}" {{ request('sap_xep') == 'gia_giam' ? 'selected' : '' }}>Giá: Cao → Thấp</option>
                    </select>
                </div>

                @if($activeCoupon ?? null)
                <div style="display:flex; align-items:center; gap:var(--space-3); background:linear-gradient(135deg,#fff3cd,#ffe69c); border:1px solid #ffc107; border-radius:var(--radius-lg); padding:var(--space-3) var(--space-4); margin-bottom:var(--space-5);">
                    <span class="material-icons" style="color:#856404; font-size:18px;">local_offer</span>
                    <span style="color:#856404; font-size:13px;">Dùng mã <strong>{{ $activeCoupon->ma_code }}</strong> để giảm {{ $activeCoupon->loai === 'percent' ? $activeCoupon->gia_tri . '%' : number_format($activeCoupon->gia_tri, 0, ',', '.') . 'đ' }} khi thanh toán!</span>
                </div>
                @endif

                    <div class="book-grid book-grid-3">
                        @forelse ($sachs as $sach)
                        @php
                            $giaBan = (float)$sach->gia_ban;
                            $giaSauGiam = null;
                            if (!empty($activeCoupon)) {
                                $giaSauGiam = $activeCoupon->loai === 'percent'
                                    ? $giaBan * (1 - $activeCoupon->gia_tri / 100)
                                    : max(0, $giaBan - $activeCoupon->gia_tri);
                                $giaSauGiam = round($giaSauGiam);
                            }
                            $pctOff = ($giaSauGiam && $giaSauGiam < $giaBan) ? round((1 - $giaSauGiam/$giaBan)*100) : 0;
                            // Fallback: gia_goc discount badge
                            if (!$pctOff && $sach->gia_goc > $sach->gia_ban) {
                                $pctOff = round((($sach->gia_goc - $sach->gia_ban) / $sach->gia_goc) * 100);
                            }
                        @endphp
                        <div class="card" id="product-{{ $sach->id }}">
                            <a href="{{ route('products.show', $sach->id) }}" style="display:block; position:relative;">
                                @php
                                    $imageUrl = $sach->link_anh_bia ?: ($sach->file_anh_bia ? asset('uploads/books/' . $sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                                @endphp
                                <img src="{{ $imageUrl }}" class="card-img" alt="{{ $sach->tieu_de }}" style="display:block;">
                                @if($pctOff > 0)
                                <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: var(--space-3);">-{{ $pctOff }}%</span>
                                @endif
                            </a>
                            <div class="card-body">
                                <div class="stars" style="margin-bottom: var(--space-2);">
                                    @php $avgStar = round($sach->trungBinhSao()); @endphp
                                    @for ($s = 1; $s <= 5; $s++)
                                        <span class="material-icons" style="font-size: 14px; color: {{ $s <= $avgStar ? '#f59e0b' : '#e2e8f0' }};">star</span>
                                    @endfor
                                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-left: var(--space-1);">({{ $sach->danhGias->count() }})</span>
                                </div>
                                <a href="{{ route('products.show', $sach->id) }}" class="card-title" style="display: block; color: var(--color-text);">{{ $sach->tieu_de }}</a>
                                <div class="card-subtitle">{{ $sach->tacGia ? $sach->tacGia->ten_tac_gia : 'Chưa cập nhật' }}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-2);">
                                    <div>
                                        @if($giaSauGiam && $giaSauGiam < $giaBan)
                                            <span class="card-price" style="color:var(--color-danger);">{{ number_format($giaSauGiam, 0, ',', '.') }}đ</span>
                                            <span style="font-size:11px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="card-price">{{ number_format($giaBan, 0, ',', '.') }}đ</span>
                                            @if($sach->gia_goc > $sach->gia_ban)
                                                <span style="font-size:11px; color:var(--color-text-muted); text-decoration:line-through; margin-left:4px;">{{ number_format($sach->gia_goc, 0, ',', '.') }}đ</span>
                                            @endif
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form">
                                        @csrf
                                        <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                                        <input type="hidden" name="so_luong" value="1">
                                        <button type="submit" class="icon-btn" title="Thêm vào giỏ" style="background: var(--color-primary-light); border-radius: var(--radius-lg); width: 36px; height: 36px; border: none; cursor: pointer; display:flex; align-items:center; justify-content:center;">
                                            <span class="material-icons" style="font-size: 18px; color: var(--color-primary-dark);">add_shopping_cart</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div style="grid-column: span 3; text-align: center; padding: var(--space-12);">
                            <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted); margin-bottom: var(--space-4);">search_off</span>
                            <p>Không có kết quả tìm kiếm phù hợp cho "<strong>{{ $queryText }}</strong>"</p>
                            <a href="{{ route('products.index') }}" class="btn btn-outline" style="margin-top: var(--space-4);">Xem tất cả sách</a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination Custom --}}
                    <div class="pagination">
                        @if (!$sachs->onFirstPage())
                            <a href="{{ $sachs->previousPageUrl() }}"><span class="material-icons">chevron_left</span></a>
                        @endif

                        @foreach ($sachs->getUrlRange(max(1, $sachs->currentPage() - 2), min($sachs->lastPage(), $sachs->currentPage() + 2)) as $page => $url)
                            @if ($page == $sachs->currentPage())
                                <span class="active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($sachs->hasMorePages())
                            <a href="{{ $sachs->nextPageUrl() }}"><span class="material-icons">chevron_right</span></a>
                        @endif
                    </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .filter-group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            margin-bottom: var(--space-3);
        }
        .filter-group-header .material-icons {
            transition: transform 0.3s;
            color: var(--color-text-muted);
        }
        .filter-group.collapsed .filter-group-content {
            display: none;
        }
        .filter-group.collapsed .material-icons {
            transform: rotate(-90deg);
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .category-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--color-primary-light) !important;
        }
        .sub-cat-link:hover {
            background: var(--color-primary-light) !important;
            color: var(--color-primary-dark) !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Radio uncheck logic
            const radios = document.querySelectorAll('.rating-radio');
            radios.forEach(radio => {
                radio.addEventListener('click', function(e) {
                    if (this.wasChecked) {
                        this.checked = false;
                        this.wasChecked = false;
                    } else {
                        radios.forEach(r => r.wasChecked = false);
                        this.wasChecked = true;
                    }
                });
                if (radio.checked) radio.wasChecked = true;
            });
        });
    </script>
    @endpush
@endsection
