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
            <button class="btn btn-outline btn-sm"><span class="material-icons" style="font-size: 16px;">download</span> Xuất Excel</button>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-6);" id="order-tabs">
        <button class="btn btn-primary btn-sm">Tất cả (312)</button>
        <button class="btn btn-ghost btn-sm">Chờ xử lý (24)</button>
        <button class="btn btn-ghost btn-sm">Đang giao (38)</button>
        <button class="btn btn-ghost btn-sm">Hoàn thành (240)</button>
        <button class="btn btn-ghost btn-sm">Đã hủy (10)</button>
    </div>

    {{-- Orders Table --}}
    <div class="table-wrapper" id="orders-table">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $orderStatuses = [
                        ['label' => 'Chờ xử lý', 'class' => 'badge-warning'],
                        ['label' => 'Đang giao', 'class' => 'badge-info'],
                        ['label' => 'Hoàn thành', 'class' => 'badge-success'],
                        ['label' => 'Đang giao', 'class' => 'badge-info'],
                        ['label' => 'Hoàn thành', 'class' => 'badge-success'],
                        ['label' => 'Đã hủy', 'class' => 'badge-danger'],
                        ['label' => 'Chờ xử lý', 'class' => 'badge-warning'],
                        ['label' => 'Hoàn thành', 'class' => 'badge-success'],
                    ];
                    $payments = ['COD', 'Chuyển khoản', 'MoMo', 'COD', 'Chuyển khoản', 'MoMo', 'COD', 'COD'];
                @endphp
                @for ($i = 0; $i < 8; $i++)
                <tr>
                    <td><input type="checkbox"></td>
                    <td style="font-weight: var(--font-semibold);">#MB2024{{ str_pad($i + 1, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div>
                            <div style="font-weight: var(--font-medium);">Khách hàng {{ $i + 1 }}</div>
                            <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">customer{{ $i + 1 }}@email.com</div>
                        </div>
                    </td>
                    <td>{{ rand(1, 5) }} sản phẩm</td>
                    <td style="font-weight: var(--font-semibold);">{{ number_format(rand(200, 900) * 1000, 0, ',', '.') }}đ</td>
                    <td>{{ $payments[$i] }}</td>
                    <td><span class="badge {{ $orderStatuses[$i]['class'] }}">{{ $orderStatuses[$i]['label'] }}</span></td>
                    <td>{{ now()->subDays($i)->format('d/m/Y') }}</td>
                    <td>
                        <div style="display: flex; gap: var(--space-1);">
                            <button class="btn btn-ghost btn-sm" title="Xem chi tiết"><span class="material-icons" style="font-size: 18px;">visibility</span></button>
                            <button class="btn btn-ghost btn-sm" title="Cập nhật"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                        </div>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination" style="margin-top: var(--space-6);">
        <a href="#"><span class="material-icons">chevron_left</span></a>
        <span class="active">1</span>
        <a href="#">2</a>
        <a href="#">3</a>
        <span>...</span>
        <a href="#">16</a>
        <a href="#"><span class="material-icons">chevron_right</span></a>
    </div>
@endsection
