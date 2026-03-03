@extends('layouts.admin')

@section('title', 'Quản lý sách')

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý sách</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <div class="header-search" style="max-width: 300px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" placeholder="Tìm kiếm sách..." id="inventory-search">
            </div>
            <button class="btn btn-primary" id="btn-add-book"><span class="material-icons" style="font-size: 18px;">add</span> Thêm sách</button>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-6);" id="inventory-tabs">
        <button class="btn btn-primary btn-sm">Tất cả (245)</button>
        <button class="btn btn-ghost btn-sm">Đang bán (198)</button>
        <button class="btn btn-ghost btn-sm">Hết hàng (12)</button>
        <button class="btn btn-ghost btn-sm">Bản nháp (35)</button>
    </div>

    {{-- Table --}}
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
                @php
                    $bookStatuses = [
                        ['label' => 'Đang bán', 'class' => 'badge-success'],
                        ['label' => 'Đang bán', 'class' => 'badge-success'],
                        ['label' => 'Hết hàng', 'class' => 'badge-danger'],
                        ['label' => 'Đang bán', 'class' => 'badge-success'],
                        ['label' => 'Bản nháp', 'class' => 'badge-warning'],
                        ['label' => 'Đang bán', 'class' => 'badge-success'],
                        ['label' => 'Đang bán', 'class' => 'badge-success'],
                        ['label' => 'Hết hàng', 'class' => 'badge-danger'],
                    ];
                    $categories = ['Tâm lý', 'Kinh doanh', 'Khoa học', 'Tiểu thuyết', 'Thiếu nhi', 'Giáo dục', 'Tâm lý', 'Kinh doanh'];
                @endphp
                @for ($i = 0; $i < 8; $i++)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <div style="width: 44px; height: 56px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span class="material-icons" style="font-size: 20px; color: var(--color-text-muted);">book</span>
                            </div>
                            <div>
                                <div style="font-weight: var(--font-medium);">Tên sách {{ $i + 1 }}</div>
                                <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Tác giả {{ $i + 1 }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-primary">{{ $categories[$i] }}</span></td>
                    <td style="font-weight: var(--font-semibold);">{{ number_format(rand(89, 299) * 1000, 0, ',', '.') }}đ</td>
                    <td>{{ $bookStatuses[$i]['label'] === 'Hết hàng' ? '0' : rand(10, 500) }}</td>
                    <td><span class="badge {{ $bookStatuses[$i]['class'] }}">{{ $bookStatuses[$i]['label'] }}</span></td>
                    <td>
                        <div style="display: flex; gap: var(--space-1);">
                            <button class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                            <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
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
        <a href="#"><span class="material-icons">chevron_right</span></a>
    </div>
@endsection
