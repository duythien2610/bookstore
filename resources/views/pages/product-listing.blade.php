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
            $layoutStyle = $isNewArrivals ? 'style="display: block;"' : '';
        @endphp
        <div class="listing-layout" {!! $layoutStyle !!}>
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
                <div id="product-list-ajax-wrapper">
                    @include('partials._product_grid')
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
            const filterForm = document.querySelector('#filter-sidebar form');
            const productWrapper = document.getElementById('product-list-ajax-wrapper');
            
            // Hàm fetch dữ liệu AJAX
            function fetchProducts(page = 1) {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                
                // Lấy giá trị sắp xếp (nếu có, từ dropdown mới)
                const sortSelect = document.getElementById('sort-select-ajax');
                if (sortSelect) {
                    params.set('sap_xep', sortSelect.value);
                }
                
                params.set('page', page);
                
                // Hiệu ứng Loading
                productWrapper.style.opacity = '0.5';
                productWrapper.style.pointerEvents = 'none';
                
                fetch('{{ route("products.index") }}?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    productWrapper.innerHTML = html;
                    productWrapper.style.opacity = '1';
                    productWrapper.style.pointerEvents = 'auto';
                    
                    // Cập nhật URL trình duyệt (không reload)
                    window.history.pushState({}, '', '{{ route("products.index") }}?' + params.toString());
                    
                    // Re-bind sự kiện cho các form Cart vừa load
                    // (Laravel layout app.blade.php thường bind DOMContentLoaded, cần bind lại nếu dùng partial)
                })
                .catch(err => console.error(err));
            }

            // Lắng nghe sự kiện change trên form (Live filter)
            if (filterForm) {
                filterForm.querySelectorAll('input').forEach(input => {
                    input.addEventListener('change', () => fetchProducts(1));
                });
                // Chặn submit form reload
                filterForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    fetchProducts(1);
                });
            }

            // Lắng nghe sự kiện Sort dropdown (Ủy quyền sự kiện vì selector nằm trong partial)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'sort-select-ajax') {
                    fetchProducts(1);
                }
            });

            // Lắng nghe sự kiện Pagination (Ủy quyền sự kiện)
            document.addEventListener('click', function(e) {
                const pageLink = e.target.closest('.ajax-pagination a');
                if (pageLink) {
                    e.preventDefault();
                    const page = pageLink.getAttribute('data-page');
                    fetchProducts(page);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });

            // Radio uncheck logic (giữ lại code cũ và tích hợp)
            const radios = document.querySelectorAll('.rating-radio');
            radios.forEach(radio => {
                radio.addEventListener('click', function(e) {
                    if (this.wasChecked) {
                        this.checked = false;
                        this.wasChecked = false;
                        fetchProducts(1); // Trigger fetch khi uncheck
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
