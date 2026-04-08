@extends('layouts.admin')

@section('title', 'Quản lý Mã Giảm Giá')

@section('content')
<div class="admin-topbar">
    <h1>Quản lý Mã Giảm Giá (Coupons)</h1>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: var(--space-6);">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger" style="margin-bottom: var(--space-6);">
    @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: var(--space-6);">
    {{-- Form Tạo Mới --}}
    <div>
        <div class="card" style="padding: var(--space-6);">
            <h2 style="font-size: var(--font-lg); margin-bottom: var(--space-4);">Tạo Mã Mới</h2>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Mã Code * (Ví dụ: SALE10K, Giam20%)</label>
                    <input type="text" name="ma_code" class="input" required style="text-transform: uppercase;">
                </div>
                <div class="form-group">
                    <label class="form-label">Loại Giảm</label>
                    <select name="loai" class="input" required>
                        <option value="fixed">Giảm số tiền cố định (VNĐ)</option>
                        <option value="percent">Giảm theo Phần trăm (%)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Giá Trị * (Ví dụ: 20000 hoặc 10)</label>
                    <input type="number" name="gia_tri" class="input" required min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày Hết Hạn</label>
                    <input type="date" name="ngay_het_han" class="input">
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: 4px;">(Bỏ
                        trống nếu mã không có hạn)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Số Lượng Giới Hạn</label>
                    <input type="number" name="so_luong" class="input" min="1">
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: 4px;">(Bỏ
                        trống nếu không giới hạn số lượng user dùng)</div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Tạo Mã Code</button>
            </form>
        </div>
    </div>

    {{-- Danh sách mã --}}
    <div>
        <div class="card table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã Code</th>
                        <th>Mức giảm</th>
                        <th>Đã dùng / Tổng</th>
                        <th>Hạn & Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $c)
                    <tr>
                        <td style="font-weight: var(--font-bold); color: var(--color-primary);">{{ $c->ma_code }}</td>
                        <td>
                            @if($c->loai == 'percent')
                            -{{ $c->gia_tri }}%
                            @else
                            -{{ number_format($c->gia_tri, 0, ',', '.') }}đ
                            @endif
                        </td>
                        <td>{{ $c->da_dung }} / {{ $c->so_luong ? $c->so_luong : '∞' }}</td>
                        <td>
                            <div><span class="badge {{ $c->trang_thai ? 'badge-success' : 'badge-danger' }}">{{
                                    $c->trang_thai ? 'Kích hoạt' : 'Đã tắt' }}</span></div>
                            @if($c->ngay_het_han)
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top:2px;">HSD: {{
                                $c->ngay_het_han->format('d/m/Y') }}
                                @if($c->ngay_het_han < now()) <span style="color:var(--color-danger);">(Hết hạn)</span>
                                    @endif
                            </div>
                            @else
                            <div style="font-size: 12px; color: var(--color-text-muted); margin-top:2px;">Vĩnh viễn
                            </div>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap: 4px;">
                                <form action="{{ route('admin.coupons.toggle', $c->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    @php $toggleColor = $c->trang_thai ? 'text-warning' : 'text-success'; @endphp
                                    <button class="btn btn-sm btn-ghost"
                                        title="{{ $c->trang_thai ? 'Tắt' : 'Bật' }} mã này">
                                        <span class="material-icons {{ $toggleColor }}">{{ $c->trang_thai ? 'pause' :
                                            'play_arrow' }}</span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn chắc chắn muốn xóa mã {{ $c->ma_code }}? Thao tác không thể hoàn tác!')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-ghost" title="Xóa vĩnh viễn"><span
                                            class="material-icons"
                                            style="color:var(--color-danger);">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 2rem;">Chưa có mã giảm giá nào được tạo.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection