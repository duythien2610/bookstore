@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-topbar">
        <h1>Dashboard</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <span style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Hôm nay: {{ now()->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid" id="stats">
        <div class="stat-card">
            <div class="stat-label">Tổng sách</div>
            <div class="stat-value">{{ $tongSach }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Đơn hàng</div>
            <div class="stat-value">{{ $tongDonHang }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Khách hàng</div>
            <div class="stat-value">{{ $tongKhachHang }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Thể loại</div>
            <div class="stat-value">{{ $tongTheLoai }}</div>
        </div>
    </div>

    {{-- Recent Books --}}
    <div style="background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); margin-bottom: var(--space-8);" id="recent-books">
        <div style="padding: var(--space-6); display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border-light);">
            <h3>Sách mới thêm gần đây</h3>
            <a href="{{ route('admin.inventory') }}" style="font-size: var(--font-size-sm);">Xem tất cả →</a>
        </div>
        <div class="table-wrapper" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên sách</th>
                        <th>Tác giả</th>
                        <th>Giá bán</th>
                        <th>Tồn kho</th>
                        <th>Ngày thêm</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sachMoi as $sach)
                    <tr>
                        <td style="font-weight: var(--font-medium);">{{ $sach->tieu_de }}</td>
                        <td>{{ $sach->tacGia->ten_tac_gia ?? '—' }}</td>
                        <td>{{ number_format($sach->gia_ban, 0, ',', '.') }}đ</td>
                        <td>{{ $sach->so_luong_ton }}</td>
                        <td style="color: var(--color-text-muted);">{{ $sach->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: var(--space-8); color: var(--color-text-muted);">
                            Chưa có dữ liệu. <a href="{{ route('admin.books.create') }}">Thêm sách ngay!</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Links --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4);">
        <a href="{{ route('admin.books.create') }}" style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); display: flex; align-items: center; gap: var(--space-4); text-decoration: none; color: var(--color-text); transition: all var(--transition-fast);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='none'">
            <div style="width: 48px; height: 48px; background: var(--color-primary-light); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: var(--color-primary-dark);">
                <span class="material-icons">add_circle</span>
            </div>
            <div>
                <div style="font-weight: var(--font-semibold);">Thêm sách</div>
                <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Thêm sách mới vào kho</div>
            </div>
        </a>
        <a href="{{ route('admin.partners') }}" style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); display: flex; align-items: center; gap: var(--space-4); text-decoration: none; color: var(--color-text); transition: all var(--transition-fast);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='none'">
            <div style="width: 48px; height: 48px; background: #eff6ff; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: #2563eb;">
                <span class="material-icons">handshake</span>
            </div>
            <div>
                <div style="font-weight: var(--font-semibold);">Đối tác</div>
                <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Quản lý tác giả, NXB, NCC</div>
            </div>
        </a>
        <a href="{{ route('admin.the-loai.index') }}" style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); display: flex; align-items: center; gap: var(--space-4); text-decoration: none; color: var(--color-text); transition: all var(--transition-fast);" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='none'">
            <div style="width: 48px; height: 48px; background: #fef3c7; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: #d97706;">
                <span class="material-icons">category</span>
            </div>
            <div>
                <div style="font-weight: var(--font-semibold);">Thể loại</div>
                <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Quản lý danh mục sách</div>
            </div>
        </a>
    </div>
@endsection
