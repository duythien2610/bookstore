@extends('layouts.admin')

@section('title', 'Quản lý sách')

@push('styles')
<style>
    /* ── Filter Panel ─────────────────────────────────────── */
    .filter-panel {
        background: var(--color-white);
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-xl);
        padding: var(--space-5) var(--space-6);
        margin-bottom: var(--space-6);
        transition: box-shadow var(--transition-base);
    }

    .filter-panel:hover {
        box-shadow: var(--shadow-sm);
    }

    .filter-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }

    .filter-toggle h3 {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        font-size: var(--font-size-sm);
        font-weight: var(--font-semibold);
        color: var(--color-text);
        margin: 0;
    }

    .filter-toggle h3 .material-icons {
        font-size: 20px;
        color: var(--color-primary);
    }

    .filter-toggle .toggle-icon {
        font-size: 20px;
        color: var(--color-text-muted);
        transition: transform var(--transition-fast);
    }

    .filter-toggle .toggle-icon.open {
        transform: rotate(180deg);
    }

    .filter-body {
        display: none;
        padding-top: var(--space-5);
        border-top: 1px solid var(--color-border-light);
        margin-top: var(--space-4);
    }

    .filter-body.show {
        display: block;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: var(--space-4);
        align-items: end;
    }

    .filter-actions {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        margin-top: var(--space-5);
    }

    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-2);
        margin-top: var(--space-3);
    }

    .active-filter-tag {
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        padding: var(--space-1) var(--space-3);
        background: var(--color-primary-light);
        color: var(--color-primary-dark);
        font-size: var(--font-size-xs);
        font-weight: var(--font-medium);
        border-radius: var(--radius-full);
    }

    .active-filter-tag .material-icons {
        font-size: 14px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity var(--transition-fast);
    }

    .active-filter-tag .material-icons:hover {
        opacity: 1;
    }

    /* ── Stats row ──────────────────────────────────────── */
    .inventory-stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: var(--space-4);
        font-size: var(--font-size-sm);
        color: var(--color-text-secondary);
    }

    /* ── New Filter Grid ─────────────────────────────────── */
    .filter-grid-new {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    @media (max-width: 992px) {
        .filter-grid-new {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .filter-grid-new {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý sách</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            {{-- Local search (real-time via AJAX). Filters by title or author. --}}
            <form action="{{ route('admin.inventory') }}" method="GET" style="max-width: 320px;" class="header-search js-admin-search-form">
                {{-- Preserve existing filters in search --}}
                @foreach(request()->except(['search', 'page']) as $key => $val)
                    @if(is_array($val))
                        @foreach($val as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @elseif($val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                <span class="material-icons search-icon">search</span>
                <input type="text" name="search"
                       placeholder="Tìm theo tên sách hoặc tác giả..."
                       value="{{ request('search') }}"
                       id="inventory-search"
                       autocomplete="off">
                <span class="js-admin-search-spinner" aria-hidden="true"></span>
                <button type="button" class="js-admin-search-clear" aria-label="Xoá tìm kiếm" title="Xoá">
                    <span class="material-icons">close</span>
                </button>
            </form>
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary" id="btn-add-book">
                <span class="material-icons" style="font-size: 18px;">add</span> Thêm sách
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
            <div style="display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-2);">
                <span class="material-icons" style="font-size: 20px;">error</span>
                <strong>Thông báo lỗi:</strong>
            </div>
            <ul style="padding-left: 28px; list-style: disc; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══════════ BỘ LỌC NÂNG CAO ═══════════ --}}
    <div class="filter-panel" style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div class="filter-toggle" id="filter-toggle" style="padding: 4px 0; border-bottom: 1px solid transparent; transition: all 0.3s;">
            <h3 style="font-size: 15px; color: #111827; display: flex; align-items: center; gap: 10px;">
                <span class="material-icons" style="color: #4f46e5; background: #eef2ff; padding: 6px; border-radius: 8px; font-size: 20px;">tune</span>
                Bộ lọc tìm kiếm
                @if(request()->hasAny(['the_loai_id', 'trang_thai', 'gia_min', 'gia_max', 'sap_xep']))
                    <span style="background: #4f46e5; color: white; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 500;">Đang hoạt động</span>
                @endif
            </h3>
            <span class="material-icons toggle-icon {{ request()->hasAny(['the_loai_id', 'trang_thai', 'gia_min', 'gia_max']) ? 'open' : '' }}" style="color: #6b7280;">expand_more</span>
        </div>

        <div class="filter-body {{ request()->hasAny(['the_loai_id', 'trang_thai', 'gia_min', 'gia_max']) ? 'show' : '' }}" id="filter-body" style="margin-top: 15px; padding-top: 20px; border-top: 1px solid #f3f4f6;">
            <form action="{{ route('admin.inventory') }}" method="GET" id="filter-form">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                <div class="filter-grid-new">
                    {{-- Cột 1: Thể loại (Dạng Scroll Checkbox) --}}
                    <div class="filter-col">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                            <span class="material-icons" style="font-size: 16px;">category</span> Thể loại sách
                        </label>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; background: #f9fafb;">
                            @foreach($theLoais as $parent)
                                {{-- Thể loại cha --}}
                                <div class="parent-category" style="margin-bottom: 4px;">
                                    <label style="display: flex; align-items: center; gap: 10px; padding: 6px; cursor: pointer; border-radius: 6px; font-size: 13px; font-weight: 600; background: #eaebf033;">
                                        <input type="checkbox" name="the_loai_id[]" value="{{ $parent->id }}" 
                                               {{ in_array($parent->id, (array)request('the_loai_id')) ? 'checked' : '' }}
                                               style="width: 16px; height: 16px; accent-color: #4f46e5;">
                                        <span style="color: #111827;">{{ $parent->ten_the_loai }}</span>
                                    </label>
                                    
                                    {{-- Thể loại con --}}
                                    @foreach($parent->children as $child)
                                        <label style="display: flex; align-items: center; gap: 10px; padding: 4px 6px 4px 30px; cursor: pointer; border-radius: 6px; font-size: 12px; transition: background 0.2s;">
                                            <input type="checkbox" name="the_loai_id[]" value="{{ $child->id }}" 
                                                   {{ in_array($child->id, (array)request('the_loai_id')) ? 'checked' : '' }}
                                                   style="width: 14px; height: 14px; accent-color: #4f46e5;">
                                            <span style="color: #4b5563;">{{ $child->ten_the_loai }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Cột 2: Trạng thái & Sắp xếp --}}
                    <div class="filter-col" style="display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <span class="material-icons" style="font-size: 16px;">bolt</span> Trạng thái kho
                            </label>
                            <select name="trang_thai" class="form-control" style="background: #f9fafb; border-radius: 8px; height: 42px;">
                                <option value="">Tất cả trạng thái</option>
                                <option value="con_hang" {{ request('trang_thai') == 'con_hang' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="het_hang" {{ request('trang_thai') == 'het_hang' ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                <span class="material-icons" style="font-size: 16px;">sort</span> Thứ tự sắp xếp
                            </label>
                            <select name="sap_xep" class="form-control" style="background: #f9fafb; border-radius: 8px; height: 42px;">
                                <option value="moi_nhat" {{ request('sap_xep', 'moi_nhat') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="gia_tang" {{ request('sap_xep') == 'gia_tang' ? 'selected' : '' }}>Giá: Thấp → Cao</option>
                                <option value="gia_giam" {{ request('sap_xep') == 'gia_giam' ? 'selected' : '' }}>Giá: Cao → Thấp</option>
                                <option value="ten_az" {{ request('sap_xep') == 'ten_az' ? 'selected' : '' }}>Tên sách: A → Z</option>
                            </select>
                        </div>
                    </div>

                    {{-- Cột 3: Khoảng giá --}}
                    <div class="filter-col">
                        <label class="form-label" style="font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <span class="material-icons" style="font-size: 16px;">payments</span> Khoảng giá bán (VNĐ)
                        </label>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px;">Từ</span>
                                <input type="number" name="gia_min" class="form-control" style="padding-left: 40px; background: #f9fafb; border-radius: 8px; height: 42px;" 
                                       placeholder="0" value="{{ request('gia_min') }}">
                            </div>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px;">Đến</span>
                                <input type="number" name="gia_max" class="form-control" style="padding-left: 40px; background: #f9fafb; border-radius: 8px; height: 42px;" 
                                       placeholder="∞" value="{{ request('gia_max') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f3f4f6; display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('admin.inventory') }}" class="btn btn-ghost" style="color: #6b7280; font-weight: 500;">Xóa trắng</a>
                    <button type="submit" class="btn btn-primary" style="padding: 0 30px; border-radius: 10px; background: #4f46e5;">
                        <span class="material-icons" style="font-size: 18px; margin-right: 6px;">search</span> Áp dụng bộ lọc
                    </button>
                </div>
            </form>
        </div>

        {{-- Active filter tags --}}
        @if(request()->hasAny(['search', 'the_loai_id', 'trang_thai', 'gia_min', 'gia_max']))
            <div class="active-filters">
                @if(request('search'))
                    <span class="active-filter-tag">
                        Tìm: "{{ request('search') }}"
                        <a href="{{ route('admin.inventory', request()->except('search')) }}"><span class="material-icons">close</span></a>
                    </span>
                @endif
                @if(request('the_loai_id'))
                    @foreach((array)request('the_loai_id') as $tid)
                        @php $selectedTL = $theLoais->firstWhere('id', $tid); @endphp
                        @if($selectedTL)
                            <span class="active-filter-tag">
                                Thể loại: {{ $selectedTL->ten_the_loai }}
                                <a href="{{ route('admin.inventory', array_merge(request()->all(), ['the_loai_id' => array_diff((array)request('the_loai_id'), [$tid])])) }}"><span class="material-icons">close</span></a>
                            </span>
                        @endif
                    @endforeach
                @endif
                @if(request('trang_thai'))
                    <span class="active-filter-tag">
                        {{ request('trang_thai') == 'con_hang' ? 'Còn hàng' : 'Hết hàng' }}
                        <a href="{{ route('admin.inventory', request()->except('trang_thai')) }}"><span class="material-icons">close</span></a>
                    </span>
                @endif
                @if(request('gia_min') || request('gia_max'))
                    <span class="active-filter-tag">
                        Giá: {{ request('gia_min') ? number_format(request('gia_min'), 0, ',', '.') . 'đ' : '0đ' }}
                        —
                        {{ request('gia_max') ? number_format(request('gia_max'), 0, ',', '.') . 'đ' : '∞' }}
                        <a href="{{ route('admin.inventory', request()->except(['gia_min', 'gia_max'])) }}"><span class="material-icons">close</span></a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- ═══════════ STATS ═══════════ --}}
    <div class="inventory-stats">
        <div style="display: flex; gap: var(--space-2);">
            <a href="{{ route('admin.inventory', request()->except('trang_thai')) }}"
               class="btn {{ !request('trang_thai') ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                Tất cả ({{ $tongTatCa }})
            </a>
            <a href="{{ route('admin.inventory', array_merge(request()->except('trang_thai'), ['trang_thai' => 'con_hang'])) }}"
               class="btn {{ request('trang_thai') == 'con_hang' ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                Còn hàng ({{ $tongConHang }})
            </a>
            <a href="{{ route('admin.inventory', array_merge(request()->except('trang_thai'), ['trang_thai' => 'het_hang'])) }}"
               class="btn {{ request('trang_thai') == 'het_hang' ? 'btn-primary' : 'btn-ghost' }} btn-sm">
                Hết hàng ({{ $tongHetHang }})
            </a>
        </div>
        <span>Hiển thị <strong>{{ $sachs->count() }}</strong> sách</span>
    </div>

    {{-- ═══════════ TABLE ═══════════ --}}
    <div class="table-wrapper js-admin-search-target" id="inventory-table"
         data-endpoint="{{ route('admin.inventory') }}" style="position: relative;">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Sách</th>
                    <th>Thể loại</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody class="js-admin-search-rows">
                @include('admin._partials.inventory_rows')
            </tbody>
        </table>
        <div class="js-admin-search-overlay" aria-hidden="true">
            <div class="js-admin-search-overlay__spinner"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Toggle filter panel
    const filterToggle = document.getElementById('filter-toggle');
    const filterBody = document.getElementById('filter-body');
    const toggleIcon = document.getElementById('toggle-icon');

    if (filterToggle) {
        filterToggle.addEventListener('click', () => {
            filterBody.classList.toggle('show');
            toggleIcon.classList.toggle('open');
        });
    }
</script>
@endpush
