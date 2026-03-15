@extends('layouts.app')

@section('title', 'Danh sách sách')
@section('meta_description', 'Khám phá hàng ngàn đầu sách chất lượng tại Modtra Books.')

@push('styles')
<style>
.filter-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 10px;
    border-radius: var(--radius-sm, 6px);
    color: var(--color-text, #333);
    text-decoration: none;
    font-size: var(--font-size-sm, .875rem);
    transition: background .15s, color .15s;
    margin-bottom: 2px;
    line-height: 1.4;
}
.filter-link:hover {
    background: var(--color-primary-light, #e8f0fe);
    color: var(--color-primary, #1a73e8);
}
.filter-link-active {
    background: var(--color-primary, #1a73e8);
    color: #fff !important;
    font-weight: 600;
}
.filter-link-active:hover {
    background: var(--color-primary-dark, #1557b0);
    color: #fff;
}
</style>
@endpush

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Tất cả sách</span>
            </div>
            <h1>Danh sách sách</h1>
        </div>
    </div>

    <div class="container">
        <div class="listing-layout">
            {{-- Filter Sidebar --}}
            <aside class="filter-sidebar" id="filter-sidebar">

                {{-- ── Tìm kiếm (vẫn dùng form, auto-submit khi nhấn Enter) ── --}}
                <form method="GET" action="{{ route('products.index') }}" id="filter-form">
                    {{-- Giữ nguyên các filter đang active --}}
                    @if(request('the_loai_id'))
                        <input type="hidden" name="the_loai_id" value="{{ request('the_loai_id') }}">
                    @endif
                    @if(request('loai_sach'))
                        <input type="hidden" name="loai_sach" value="{{ request('loai_sach') }}">
                    @endif
                    @if(request('gia_min'))
                        <input type="hidden" name="gia_min" value="{{ request('gia_min') }}">
                    @endif
                    @if(request('gia_max'))
                        <input type="hidden" name="gia_max" value="{{ request('gia_max') }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ request('sort', 'moi_nhat') }}">

                    <div class="filter-group">
                        <h4>Tìm kiếm</h4>
                        <div style="position: relative;">
                            <input type="text" name="search" id="sidebar-search" class="form-control"
                                placeholder="Sách, tác giả, NXB, NCC..."
                                value="{{ request('search') }}"
                                style="padding-right: 36px;">
                            <button type="submit" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:var(--color-text-muted);">
                                <span class="material-icons" style="font-size:18px;vertical-align:middle;">search</span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- ── Khoảng giá (form riêng, submit khi nhấn nút) ── --}}
                <form method="GET" action="{{ route('products.index') }}" id="price-form">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('the_loai_id'))
                        <input type="hidden" name="the_loai_id" value="{{ request('the_loai_id') }}">
                    @endif
                    @if(request('loai_sach'))
                        <input type="hidden" name="loai_sach" value="{{ request('loai_sach') }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ request('sort', 'moi_nhat') }}">

                    <div class="filter-group">
                        <h4>Khoảng giá</h4>
                        <div style="display: flex; gap: var(--space-2); align-items: center; margin-bottom: var(--space-2);">
                            <input type="number" name="gia_min" class="form-control" placeholder="Từ"
                                value="{{ request('gia_min') }}" style="padding: var(--space-2);">
                            <span>—</span>
                            <input type="number" name="gia_max" class="form-control" placeholder="Đến"
                                value="{{ request('gia_max') }}" style="padding: var(--space-2);">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-sm">Lọc giá</button>
                        @if(request('gia_min') || request('gia_max'))
                            <a href="{{ request()->fullUrlWithQuery(['gia_min' => null, 'gia_max' => null]) }}"
                               style="display:block;margin-top:var(--space-1);font-size:var(--font-size-xs);color:var(--color-text-muted);text-align:center;">
                                ✕ Bỏ lọc giá
                            </a>
                        @endif
                    </div>
                </form>

                {{-- ── Loại sách (link trực tiếp) ── --}}
                @php
                    $baseParams = array_filter(request()->only(['search','the_loai_id','gia_min','gia_max','sort']));
                @endphp
                <div class="filter-group">
                    <h4>Loại sách</h4>
                    <div style="display:flex;flex-direction:column;gap:var(--space-1);">
                        @php
                            $loaiSachActive = request('loai_sach','');
                        @endphp
                        <a href="{{ route('products.index', array_merge($baseParams, ['loai_sach'=>''])) }}"
                           class="filter-link {{ $loaiSachActive === '' ? 'filter-link-active' : '' }}">
                            <span class="material-icons" style="font-size:16px;">apps</span> Tất cả
                        </a>
                        <a href="{{ route('products.index', array_merge($baseParams, ['loai_sach'=>'trong_nuoc'])) }}"
                           class="filter-link {{ $loaiSachActive === 'trong_nuoc' ? 'filter-link-active' : '' }}">
                            <span class="material-icons" style="font-size:16px;">flag</span> Trong nước
                        </a>
                        <a href="{{ route('products.index', array_merge($baseParams, ['loai_sach'=>'nuoc_ngoai'])) }}"
                           class="filter-link {{ $loaiSachActive === 'nuoc_ngoai' ? 'filter-link-active' : '' }}">
                            <span class="material-icons" style="font-size:16px;">language</span> Nước ngoài
                        </a>
                    </div>
                </div>

                {{-- ── Thể loại (link trực tiếp, giữ nguyên loai_sach nếu có) ── --}}
                @php
                    $loaiSachFilter = request('loai_sach');
                    if ($loaiSachFilter === 'trong_nuoc') {
                        $sidebarCategories = $menuCategoriesTrongNuoc;
                    } elseif ($loaiSachFilter === 'nuoc_ngoai') {
                        $sidebarCategories = $menuCategoriesNuocNgoai;
                    } else {
                        $sidebarCategories = $menuCategories;
                    }
                    $activeTlId = request('the_loai_id');
                    // Params giữ lại khi click thể loại (bỏ the_loai_id cũ)
                    $tlBaseParams = array_filter(request()->only(['search','loai_sach','gia_min','gia_max','sort']));
                @endphp
                <div class="filter-group">
                    <h4>
                        Thể loại
                        @if($activeTlId)
                            <a href="{{ route('products.index', $tlBaseParams) }}"
                               style="font-size:var(--font-size-xs);color:var(--color-text-muted);margin-left:var(--space-2);font-weight:400;">
                                ✕ Xóa
                            </a>
                        @endif
                    </h4>
                    <div class="filter-scrollable" style="max-height:360px;overflow-y:auto;">
                        @foreach ($sidebarCategories as $parent)
                            {{-- Link danh mục Cha --}}
                            <a href="{{ route('products.index', array_merge($tlBaseParams, ['the_loai_id' => $parent->id])) }}"
                               class="filter-link {{ $activeTlId == $parent->id ? 'filter-link-active' : '' }}"
                               style="font-weight:600;">
                                {{ $parent->ten_the_loai }}
                                @if($parent->children->isNotEmpty())
                                    <span class="material-icons" style="font-size:14px;float:right;margin-top:2px;opacity:.5;">expand_more</span>
                                @endif
                            </a>
                            {{-- Link danh mục Con --}}
                            @if($parent->children->isNotEmpty())
                                <div style="margin-left:var(--space-4);border-left:2px solid var(--color-border);padding-left:var(--space-2);margin-bottom:var(--space-1);">
                                    @foreach($parent->children as $child)
                                        <a href="{{ route('products.index', array_merge($tlBaseParams, ['the_loai_id' => $child->id])) }}"
                                           class="filter-link {{ $activeTlId == $child->id ? 'filter-link-active' : '' }}"
                                           style="font-size:var(--font-size-sm);">
                                            {{ $child->ten_the_loai }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Nút xóa tất cả bộ lọc --}}
                @if(request()->hasAny(['search','the_loai_id','loai_sach','gia_min','gia_max']))
                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-block btn-sm" style="margin-top:var(--space-2);">
                        <span class="material-icons" style="font-size:14px;vertical-align:middle;">close</span> Xóa tất cả bộ lọc
                    </a>
                @endif

            </aside>


            {{-- Product Grid --}}
            <div>
                <div class="listing-topbar">
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">
                        Hiển thị <strong>{{ $sachs->firstItem() ?? 0 }}–{{ $sachs->lastItem() ?? 0 }}</strong>
                        trong <strong>{{ $sachs->total() }}</strong> sách
                    </p>
                    <select class="form-control" style="width: auto;" id="sort-select" onchange="changeSortAndSubmit(this.value)">
                        <option value="moi_nhat" {{ request('sort','moi_nhat') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="gia_tang"  {{ request('sort') == 'gia_tang' ? 'selected' : '' }}>Giá: Thấp → Cao</option>
                        <option value="gia_giam"  {{ request('sort') == 'gia_giam' ? 'selected' : '' }}>Giá: Cao → Thấp</option>
                        <option value="ten_az"    {{ request('sort') == 'ten_az'  ? 'selected' : '' }}>Tên A → Z</option>
                    </select>
                </div>

                @if ($sachs->isEmpty())
                <div style="text-align: center; padding: var(--space-16) 0;">
                    <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">search_off</span>
                    <p style="margin-top: var(--space-4); color: var(--color-text-muted);">Không tìm thấy sách phù hợp.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: var(--space-4);">Xem tất cả sách</a>
                </div>
                @else
                <div class="book-grid book-grid-3">
                    @foreach ($sachs as $sach)
                    <div class="card" id="product-{{ $sach->id }}">
                        <a href="{{ route('products.show', $sach->id) }}" style="text-decoration: none; color: inherit;">
                            <div style="position: relative;">
                                <div class="card-img" style="display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    @if ($sach->file_anh_bia)
                                        <img src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @elseif ($sach->link_anh_bia)
                                        <img src="{{ $sach->link_anh_bia }}" alt="{{ $sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                        <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted); display: none;">book</span>
                                    @else
                                        <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                                    @endif
                                </div>
                                @if ($sach->gia_goc > 0 && $sach->gia_goc > $sach->gia_ban)
                                <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: var(--space-3);">
                                    -{{ round(($sach->gia_goc - $sach->gia_ban) / $sach->gia_goc * 100) }}%
                                </span>
                                @endif
                                @if ($sach->loai_sach === 'nuoc_ngoai')
                                <span class="badge badge-primary" style="position: absolute; top: var(--space-3); right: var(--space-3);">Nước ngoài</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="card-title" style="
                                    display: -webkit-box;
                                    -webkit-line-clamp: 2;
                                    -webkit-box-orient: vertical;
                                    overflow: hidden;
                                    min-height: 2.8em;
                                    line-clamp: 2;
                                ">{{ $sach->tieu_de }}</div>
                                <div class="card-subtitle">{{ $sach->tacGia->ten_tac_gia ?? 'Không rõ' }}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-2);">
                                    <div>
                                        <div class="card-price">{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</div>
                                        @if ($sach->gia_goc > 0 && $sach->gia_goc > $sach->gia_ban)
                                        <div style="font-size: var(--font-size-xs); color: var(--color-text-muted); text-decoration: line-through;">
                                            {{ number_format($sach->gia_goc, 0, ',', '.') }}đ
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        <div style="padding: 0 var(--space-4) var(--space-4);">
                            <form method="POST" action="{{ route('cart.add') }}" class="ajax-cart-form">
                                @csrf
                                <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                                <input type="hidden" name="so_luong" value="1">
                                @auth
                                    @if (Auth::user()->email_verified_at)
                                        <button type="submit" class="btn btn-primary btn-block btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                            <span class="material-icons" style="font-size: 16px;">add_shopping_cart</span> Thêm vào giỏ
                                        </button>
                                    @else
                                        <a href="{{ route('verification.notice') }}" class="btn btn-outline btn-block btn-sm">Xác thực để mua</a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline btn-block btn-sm">Đăng nhập để mua</a>
                                @endauth
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div style="margin-top: var(--space-8);">
                    {{ $sachs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

@push('scripts')
<script>
function changeSortAndSubmit(value) {
    // Xây dựng URL mới giữ lại tất cả query params hiện tại, chỉ đổi 'sort'
    const url = new URL(window.location.href);
    url.searchParams.set('sort', value);
    url.searchParams.delete('page'); // về trang 1 khi đổi sort
    window.location.href = url.toString();
}
</script>
@endpush
@endsection
