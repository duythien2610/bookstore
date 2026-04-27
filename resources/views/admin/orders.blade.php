@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý đơn hàng ({{ $tongTatCa }})</h1>
        <form action="{{ route('admin.orders') }}" method="GET" class="js-admin-search-form" style="display: flex; align-items: center; gap: var(--space-3);">
            <div class="header-search" style="max-width: 280px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mã đơn, tên khách, SĐT..." id="order-search" autocomplete="off">
                <span class="js-admin-search-spinner" aria-hidden="true"></span>
                <button type="button" class="js-admin-search-clear" aria-label="Xoá tìm kiếm" title="Xoá">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div style="display: flex; align-items: center; gap: var(--space-2);">
                <span style="font-size: 13px; color: var(--color-text-muted);">Ngày:</span>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control js-admin-search-extra" style="padding: 6px 10px; font-size: 13px; width: 140px; height: 40px; border-radius: 8px;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="height: 40px; padding: 0 20px;">Lọc</button>
            @if(request()->hasAny(['search', 'date', 'trang_thai']))
                <a href="{{ route('admin.orders') }}" class="btn btn-ghost btn-sm" style="height: 40px;">Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: var(--space-6);">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter Tabs --}}
    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-6); overflow-x: auto; padding-bottom: 4px;" id="order-tabs">
        <a href="{{ route('admin.orders') }}" class="btn btn-sm {{ !request('trang_thai') ? 'btn-primary' : 'btn-outline' }}">
            Tất cả ({{ array_sum($statusCounts) }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'cho_thanh_toan']) }}" class="btn btn-sm {{ request('trang_thai') == 'cho_thanh_toan' ? 'btn-primary' : 'btn-outline' }}">
            Chờ thanh toán ({{ $statusCounts['cho_thanh_toan'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'cho_xac_nhan']) }}" class="btn btn-sm {{ request('trang_thai') == 'cho_xac_nhan' ? 'btn-primary' : 'btn-outline' }}">
            Chờ xác nhận ({{ $statusCounts['cho_xac_nhan'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'dang_xu_ly']) }}" class="btn btn-sm {{ request('trang_thai') == 'dang_xu_ly' ? 'btn-primary' : 'btn-outline' }}">
            Đang lấy hàng ({{ $statusCounts['dang_xu_ly'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'dang_giao']) }}" class="btn btn-sm {{ request('trang_thai') == 'dang_giao' ? 'btn-primary' : 'btn-outline' }}">
            Đang giao ({{ $statusCounts['dang_giao'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'da_giao']) }}" class="btn btn-sm {{ request('trang_thai') == 'da_giao' ? 'btn-primary' : 'btn-outline' }}">
            Hoàn thành ({{ $statusCounts['da_giao'] ?? 0 }})
        </a>
        <a href="{{ route('admin.orders', ['trang_thai' => 'huy']) }}" class="btn btn-sm {{ request('trang_thai') == 'huy' ? 'btn-primary' : 'btn-outline' }}">
            Đã hủy ({{ $statusCounts['huy'] ?? 0 }})
        </a>
    </div>

    {{-- Orders Table --}}
    <div class="table-wrapper js-admin-search-target" id="orders-table"
         data-endpoint="{{ route('admin.orders') }}" style="position: relative;">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>P.Thức</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody class="js-admin-search-rows">
                @include('admin._partials.orders_rows')
            </tbody>
        </table>
        <div class="js-admin-search-overlay" aria-hidden="true">
            <div class="js-admin-search-overlay__spinner"></div>
        </div>
    </div>

    @if($donHangs->hasPages())
    <div style="margin-top: var(--space-6);">
        {{ $donHangs->links('pagination::bootstrap-4') }}
    </div>
    @endif
@endsection
