@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý đơn hàng</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <div class="header-search" style="max-width: 300px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" placeholder="Tìm đơn hàng..." id="order-search">
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-6);" id="order-tabs">
        <button class="btn btn-primary btn-sm">Tất cả ({{ $donHangs->count() }})</button>
    </div>

    {{-- Orders Table --}}
    <div class="table-wrapper" id="orders-table">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donHangs as $dh)
                <tr>
                    <td><input type="checkbox"></td>
                    <td style="font-weight: var(--font-semibold);">#MB{{ str_pad($dh->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div>
                            <div style="font-weight: var(--font-medium);">{{ $dh->user->ho_ten ?? 'Khách vãng lai' }}</div>
                        </div>
                    </td>
                    <td style="font-weight: var(--font-semibold);">{{ number_format($dh->tong_tien ?? 0, 0, ',', '.') }}đ</td>
                    <td>
                        @if($dh->trang_thai === 'hoan_thanh')
                            <span class="badge badge-success">Hoàn thành</span>
                        @elseif($dh->trang_thai === 'dang_giao')
                            <span class="badge badge-info">Đang giao</span>
                        @elseif($dh->trang_thai === 'da_huy')
                            <span class="badge badge-danger">Đã hủy</span>
                        @else
                            <span class="badge badge-warning">Chờ xử lý</span>
                        @endif
                    </td>
                    <td style="color: var(--color-text-muted);">{{ $dh->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display: flex; gap: var(--space-1);">
                            <button class="btn btn-ghost btn-sm" title="Xem chi tiết"><span class="material-icons" style="font-size: 18px;">visibility</span></button>
                            <button class="btn btn-ghost btn-sm" title="Cập nhật"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: var(--space-10); color: var(--color-text-muted);">
                        <span class="material-icons" style="font-size: 48px; display: block; margin-bottom: var(--space-3);">receipt_long</span>
                        Chưa có đơn hàng nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
