@extends('layouts.admin')

@section('title', 'Quản lý Banner')

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý Banner Khuyến mãi</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: var(--space-6);">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: var(--space-6);">
        @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2.5fr; gap: var(--space-6);">
        {{-- Form Thêm Banner --}}
        <div>
            <div class="card" style="padding: var(--space-6); margin-bottom: var(--space-6);">
                <h2 style="font-size: var(--font-lg); margin-bottom: var(--space-4);">Thêm Banner Mới</h2>
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tiêu đề (Tùy chọn)</label>
                        <input type="text" name="tieu_de" class="input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tải ảnh lên (Khuyên dùng 1600x600px)</label>
                        <input type="file" name="anh_file" class="input" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hoặc Link ảnh Online</label>
                        <input type="url" name="link_anh" class="input" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Link chuyển hướng khi click (URL)</label>
                        <input type="url" name="lien_ket" class="input" placeholder="VD: /products?gia_goc=1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vị trí hiển thị</label>
                        <select name="vi_tri" class="input">
                            <option value="hero">Hero (Trang Chủ)</option>
                            <option value="sidebar">Sidebar (Cột bên)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Thứ tự hiển thị (Càng nhỏ càng hiện trước)</label>
                        <input type="number" name="thu_tu" class="input" value="0" min="0">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tạo Banner</button>
                </form>
            </div>
        </div>

        {{-- Danh sách Banners --}}
        <div>
            <div class="card table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Chi tiết</th>
                            <th>Vị trí</th>
                            <th>Thứ tự</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $b)
                        <tr>
                            <td style="width: 120px;">
                                @if($b->anhSrc)
                                    <img src="{{ $b->anhSrc }}" style="width:100%; height:60px; object-fit:cover; border-radius:4px;">
                                @else
                                    <div style="width:100%; height:60px; background:#eee; display:flex; align-items:center; justify-content:center;">No Image</div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: var(--font-bold); margin-bottom:4px;">{{ $b->tieu_de ?? 'Trống' }}</div>
                                @if($b->lien_ket)
                                <div style="font-size: 11px; color: var(--color-primary); word-break: break-all;">
                                    <a href="{{ $b->lien_ket }}" target="_blank">🔗 {{ $b->lien_ket }}</a>
                                </div>
                                @endif
                            </td>
                            <td><span class="badge" style="background:var(--color-bg); color:var(--color-text-muted);">{{ strtoupper($b->vi_tri) }}</span></td>
                            <td>{{ $b->thu_tu }}</td>
                            <td>
                                <form action="{{ route('admin.banners.toggle', $b->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="badge {{ $b->trang_thai ? 'badge-success' : 'badge-danger' }}" style="border:none; cursor:pointer;" title="Click để {{ $b->trang_thai ? 'Tắt' : 'Bật' }}">
                                        {{ $b->trang_thai ? 'Hiển thị' : 'Đang Tắt' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div style="display:flex; gap: 4px;">
                                    <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Xóa banner này vĩnh viễn?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-ghost"><span class="material-icons" style="color:var(--color-danger);">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2rem;">Chưa có banner nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
