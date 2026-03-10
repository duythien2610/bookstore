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

    @media (max-width: 768px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý sách</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            {{-- Search form --}}
            <form action="{{ route('admin.inventory') }}" method="GET" style="max-width: 300px;" class="header-search">
                {{-- Preserve existing filters in search --}}
                @foreach(request()->except(['search', 'page']) as $key => $val)
                    @if($val) <input type="hidden" name="{{ $key }}" value="{{ $val }}"> @endif
                @endforeach
                <span class="material-icons search-icon">search</span>
                <input type="text" name="search" placeholder="Tìm kiếm sách..." value="{{ request('search') }}" id="inventory-search">
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

    {{-- ═══════════ BỘ LỌC ═══════════ --}}
    <div class="filter-panel">
        <div class="filter-toggle" id="filter-toggle">
            <h3>
                <span class="material-icons">tune</span>
                Bộ lọc nâng cao
                @if(request()->hasAny(['the_loai_id', 'trang_thai', 'gia_min', 'gia_max', 'sap_xep']))
                    <span class="badge badge-primary" style="font-size: 11px; padding: 2px 8px;">Đang lọc</span>
                @endif
            </h3>
            <span class="material-icons toggle-icon {{ request()->hasAny(['the_loai_id', 'trang_thai', 'gia_min', 'gia_max']) ? 'open' : '' }}" id="toggle-icon">expand_more</span>
        </div>

        <div class="filter-body {{ request()->hasAny(['the_loai_id', 'trang_thai', 'gia_min', 'gia_max']) ? 'show' : '' }}" id="filter-body">
            <form action="{{ route('admin.inventory') }}" method="GET" id="filter-form">
                {{-- Preserve search query --}}
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="filter-grid">
                    {{-- Thể loại --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="the_loai_id" style="font-size: var(--font-size-xs); margin-bottom: var(--space-1);">Thể loại</label>
                        <select name="the_loai_id" id="the_loai_id" class="form-control" style="font-size: var(--font-size-sm);">
                            <option value="">Tất cả thể loại</option>
                            @foreach($theLoais as $tl)
                                <option value="{{ $tl->id }}" {{ request('the_loai_id') == $tl->id ? 'selected' : '' }}>
                                    {{ $tl->ten_the_loai }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Trạng thái --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="trang_thai" style="font-size: var(--font-size-xs); margin-bottom: var(--space-1);">Trạng thái</label>
                        <select name="trang_thai" id="trang_thai" class="form-control" style="font-size: var(--font-size-sm);">
                            <option value="">Tất cả</option>
                            <option value="con_hang" {{ request('trang_thai') == 'con_hang' ? 'selected' : '' }}>Còn hàng</option>
                            <option value="het_hang" {{ request('trang_thai') == 'het_hang' ? 'selected' : '' }}>Hết hàng</option>
                        </select>
                    </div>

                    {{-- Giá từ --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="gia_min" style="font-size: var(--font-size-xs); margin-bottom: var(--space-1);">Giá từ (VNĐ)</label>
                        <input type="number" name="gia_min" id="gia_min" class="form-control" style="font-size: var(--font-size-sm);"
                               placeholder="VD: 50000" min="0" step="1" value="{{ request('gia_min') }}">
                    </div>

                    {{-- Giá đến --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="gia_max" style="font-size: var(--font-size-xs); margin-bottom: var(--space-1);">Giá đến (VNĐ)</label>
                        <input type="number" name="gia_max" id="gia_max" class="form-control" style="font-size: var(--font-size-sm);"
                               placeholder="VD: 300000" min="0" step="1" value="{{ request('gia_max') }}">
                    </div>

                    {{-- Sắp xếp --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="sap_xep" style="font-size: var(--font-size-xs); margin-bottom: var(--space-1);">Sắp xếp</label>
                        <select name="sap_xep" id="sap_xep" class="form-control" style="font-size: var(--font-size-sm);">
                            <option value="moi_nhat" {{ request('sap_xep', 'moi_nhat') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="gia_tang" {{ request('sap_xep') == 'gia_tang' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="gia_giam" {{ request('sap_xep') == 'gia_giam' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="ten_az" {{ request('sap_xep') == 'ten_az' ? 'selected' : '' }}>Tên A → Z</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span class="material-icons" style="font-size: 16px;">filter_list</span> Áp dụng lọc
                    </button>
                    <a href="{{ route('admin.inventory') }}" class="btn btn-ghost btn-sm">
                        <span class="material-icons" style="font-size: 16px;">clear_all</span> Xóa lọc
                    </a>
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
                    @php $selectedTL = $theLoais->firstWhere('id', request('the_loai_id')); @endphp
                    @if($selectedTL)
                        <span class="active-filter-tag">
                            Thể loại: {{ $selectedTL->ten_the_loai }}
                            <a href="{{ route('admin.inventory', request()->except('the_loai_id')) }}"><span class="material-icons">close</span></a>
                        </span>
                    @endif
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
    <div class="table-wrapper" id="inventory-table">
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
            <tbody>
                @forelse($sachs as $sach)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <div style="width: 44px; height: 56px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                @if($sach->file_anh_bia)
                                    <img src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @elseif($sach->link_anh_bia)
                                    <img src="{{ $sach->link_anh_bia }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span class="material-icons" style="font-size: 20px; color: var(--color-text-muted);">book</span>
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: var(--font-medium);">{{ $sach->tieu_de }}</div>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">{{ $sach->tacGia->ten_tac_gia ?? 'Chưa có tác giả' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($sach->theLoai)
                            <span class="badge badge-primary">{{ $sach->theLoai->ten_the_loai }}</span>
                        @else
                            <span style="color: var(--color-text-muted); font-size: var(--font-size-xs);">—</span>
                        @endif
                    </td>
                    <td style="font-weight: var(--font-semibold);">{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</td>
                    <td>{{ $sach->so_luong_ton }}</td>
                    <td>
                        @if($sach->so_luong_ton > 0)
                            <span class="badge badge-success">Còn hàng</span>
                        @else
                            <span class="badge badge-danger">Hết hàng</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: var(--space-1);">
                            <a href="{{ route('admin.books.edit', $sach->id) }}" class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 18px;">edit</span></a>
                            <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: var(--space-10); color: var(--color-text-muted);">
                        <span class="material-icons" style="font-size: 48px; display: block; margin-bottom: var(--space-3);">inventory_2</span>
                        @if(request()->hasAny(['search', 'the_loai_id', 'trang_thai', 'gia_min', 'gia_max']))
                            Không tìm thấy sách nào phù hợp với bộ lọc.
                            <br>
                            <a href="{{ route('admin.inventory') }}" class="btn btn-outline" style="margin-top: var(--space-4);">Xóa bộ lọc</a>
                        @else
                            Chưa có sách nào trong hệ thống.
                            <br>
                            <a href="{{ route('admin.books.create') }}" class="btn btn-primary" style="margin-top: var(--space-4);">Thêm sách đầu tiên</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
