@extends('layouts.app')

@section('title', 'Danh sách sách')

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
                <div class="filter-group">
                    <h4>Thể loại</h4>
                    @php $cats = ['Tâm lý', 'Kinh doanh', 'Khoa học', 'Tiểu thuyết', 'Thiếu nhi', 'Giáo dục']; @endphp
                    @foreach ($cats as $cat)
                    <div class="form-check" style="margin-bottom: var(--space-2);">
                        <input type="checkbox" id="cat-{{ $loop->index }}" name="category[]" value="{{ $cat }}">
                        <label for="cat-{{ $loop->index }}">{{ $cat }}</label>
                    </div>
                    @endforeach
                </div>

                <div class="filter-group">
                    <h4>Khoảng giá</h4>
                    <div style="display: flex; gap: var(--space-2); align-items: center;">
                        <input type="number" class="form-control" placeholder="Từ" style="padding: var(--space-2);">
                        <span>—</span>
                        <input type="number" class="form-control" placeholder="Đến" style="padding: var(--space-2);">
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Đánh giá</h4>
                    @for ($r = 5; $r >= 3; $r--)
                    <div class="form-check" style="margin-bottom: var(--space-2);">
                        <input type="radio" id="rating-{{ $r }}" name="rating" value="{{ $r }}">
                        <label for="rating-{{ $r }}">
                            <span class="stars" style="display: inline-flex;">
                                @for ($s = 1; $s <= 5; $s++)
                                    <span class="material-icons" style="font-size: 14px;">{{ $s <= $r ? 'star' : 'star' }}</span>
                                @endfor
                            </span>
                            trở lên
                        </label>
                    </div>
                    @endfor
                </div>

                <button class="btn btn-primary btn-block" id="btn-apply-filter">Áp dụng bộ lọc</button>
            </aside>

            {{-- Product Grid --}}
            <div>
                <div class="listing-topbar">
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Hiển thị <strong>1-20</strong> trong <strong>245</strong> sách</p>
                    <select class="form-control" style="width: auto;" id="sort-select">
                        <option>Mới nhất</option>
                        <option>Bán chạy</option>
                        <option>Giá: Thấp → Cao</option>
                        <option>Giá: Cao → Thấp</option>
                        <option>Đánh giá cao</option>
                    </select>
                </div>

                <div class="book-grid book-grid-3">
                    @for ($i = 1; $i <= 9; $i++)
                    <div class="card" id="product-{{ $i }}">
                        <div style="position: relative;">
                            <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                                <span class="material-icons" style="font-size: 64px; color: var(--color-text-muted);">book</span>
                            </div>
                            @if ($i <= 3)
                            <span class="badge badge-danger" style="position: absolute; top: var(--space-3); left: var(--space-3);">-{{ $i * 10 }}%</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="stars" style="margin-bottom: var(--space-2);">
                                @for ($s = 1; $s <= 5; $s++)
                                    <span class="material-icons" style="font-size: 14px;">{{ $s <= 4 ? 'star' : 'star' }}</span>
                                @endfor
                                <span style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-left: var(--space-1);">({{ rand(12, 99) }})</span>
                            </div>
                            <div class="card-title">Tên sách {{ $i }}</div>
                            <div class="card-subtitle">Tác giả {{ $i }}</div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-2);">
                                <div class="card-price">{{ number_format(rand(89, 299) * 1000, 0, ',', '.') }}đ</div>
                                <button class="icon-btn" title="Thêm vào giỏ" style="background: var(--color-primary-light); border-radius: var(--radius-lg); width: 36px; height: 36px; border: none; cursor: pointer;">
                                    <span class="material-icons" style="font-size: 18px; color: var(--color-primary-dark);">add_shopping_cart</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- Pagination --}}
                <div class="pagination" id="pagination">
                    <a href="#"><span class="material-icons">chevron_left</span></a>
                    <span class="active">1</span>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">4</a>
                    <span>...</span>
                    <a href="#">13</a>
                    <a href="#"><span class="material-icons">chevron_right</span></a>
                </div>
            </div>
        </div>
    </div>
@endsection
