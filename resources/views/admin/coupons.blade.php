@extends('layouts.admin')

@section('title', 'Quản lý Mã Giảm Giá')

@section('content')
    <div class="admin-topbar" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-3);">
        <h1>Quản lý Mã Giảm Giá (Coupons)</h1>
        <div style="display:flex;gap:var(--space-2); flex-wrap:wrap;">
            <a href="{{ route('admin.coupons.export') }}" class="btn btn-outline btn-sm">
                <span class="material-icons" style="font-size:18px;">download</span> Xuất CSV
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('import-modal').style.display='flex'">
                <span class="material-icons" style="font-size:18px;">upload</span> Nhập CSV
            </button>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="import-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:var(--color-white); border-radius:var(--radius-xl); padding:var(--space-8); max-width:480px; width:90%; box-shadow: 0 25px 50px rgba(0,0,0,.3);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-6);">
                <h2 style="font-size:var(--font-lg);">Nhập mã từ file CSV</h2>
                <button onclick="document.getElementById('import-modal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--color-text-muted);">✕</button>
            </div>
            <div style="background:var(--color-bg-alt); border-radius:var(--radius-md); padding:var(--space-4); margin-bottom:var(--space-4); font-size:13px; color:var(--color-text-muted);">
                <strong>Định dạng CSV:</strong><br>
                <code>ma_code, loai, gia_tri, ngay_het_han, so_luong, da_dung, trang_thai</code><br><br>
                • <code>loai</code>: <code>percent</code> hoặc <code>fixed</code><br>
                • <code>ngay_het_han</code>: <code>YYYY-MM-DD</code> (bỏ trống = vĩnh viễn)<br>
                • <code>trang_thai</code>: 1 = kích hoạt, 0 = tắt
            </div>
            <form action="{{ route('admin.coupons.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="margin-bottom:var(--space-6);">
                    <label class="form-label">Chọn file CSV *</label>
                    <input type="file" name="csv_file" class="input" accept=".csv,.txt" required>
                </div>
                <div style="display:flex; gap:var(--space-3);">
                    <button type="submit" class="btn btn-primary" style="flex:1;">Nhập dữ liệu</button>
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('import-modal').style.display='none'">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: var(--space-4);">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: var(--space-4);">
        @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
    </div>
    @endif
    @if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="alert" style="background:#fff3cd; border:1px solid #ffc107; color:#856404; border-radius:var(--radius-md); padding:var(--space-4); margin-bottom:var(--space-4);">
        <strong>Chi tiết lỗi khi nhập:</strong>
        <ul style="margin:var(--space-2) 0 0 var(--space-4); list-style:disc;">
            @foreach(session('import_errors') as $err)
                <li style="font-size:13px;">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $theLoais = \App\Models\TheLoai::whereNull('parent_id')->orderBy('ten_the_loai')->get();
    @endphp

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: var(--space-6); align-items:start;">
        {{-- ── FORM TẠO MỚI ── --}}
        <div>
            <div class="card" style="padding: var(--space-6);">
                <h2 style="font-size: var(--font-lg); margin-bottom: var(--space-4);">Tạo Mã Mới</h2>
                <form action="{{ route('admin.coupons.store') }}" method="POST" id="coupon-form">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Mã Code * <span style="font-size:11px; color:var(--color-text-muted);">(VD: SALE10K)</span></label>
                        <input type="text" name="ma_code" class="form-control @error('ma_code') is-invalid @enderror" required style="text-transform:uppercase;" value="{{ old('ma_code') }}">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-3);">
                        <div class="form-group">
                            <label class="form-label">Loại Giảm</label>
                            <select name="loai" class="form-control" required>
                                <option value="fixed" {{ old('loai') == 'fixed' ? 'selected' : '' }}>Cố định (VNĐ)</option>
                                <option value="percent" {{ old('loai') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Giá Trị *</label>
                            <input type="number" name="gia_tri" class="form-control @error('gia_tri') is-invalid @enderror" required min="1" value="{{ old('gia_tri') }}">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-3);">
                        <div class="form-group">
                            <label class="form-label">Ngày Hết Hạn</label>
                            <input type="date" name="ngay_het_han" class="form-control" value="{{ old('ngay_het_han') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số Lượng Dùng</label>
                            <input type="number" name="so_luong" class="form-control" min="1" placeholder="∞" value="{{ old('so_luong') }}">
                        </div>
                    </div>

                    {{-- ── ĐIỀU KIỆN ĐƠN HÀNG ── --}}
                    <div style="border-top:1px solid var(--color-border); margin:var(--space-4) 0; padding-top:var(--space-4);">
                        <div style="font-size:13px; font-weight:600; color:var(--color-text-muted); margin-bottom:var(--space-3); text-transform:uppercase; letter-spacing:0.5px;">🎯 Phạm vi áp dụng</div>

                        <div class="form-group">
                            <select name="pham_vi" class="form-control" id="sel-pham-vi" onchange="togglePhamVi(this.value)">
                                <option value="all"      {{ old('pham_vi','all') == 'all'      ? 'selected' : '' }}>🌐 Toàn bộ sản phẩm</option>
                                <option value="category" {{ old('pham_vi') == 'category' ? 'selected' : '' }}>📂 Theo thể loại</option>
                                <option value="book"     {{ old('pham_vi') == 'book'     ? 'selected' : '' }}>📖 Theo sách cụ thể</option>
                            </select>
                        </div>

                        {{-- Chọn thể loại --}}
                        <div id="group-category" style="{{ old('pham_vi') == 'category' ? '' : 'display:none;' }} background:var(--color-bg-alt); border-radius:var(--radius-md); padding:var(--space-3); margin-bottom:var(--space-3);">
                            <label class="form-label" style="margin-bottom:var(--space-2);">Chọn thể loại (có thể chọn nhiều):</label>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; max-height:160px; overflow-y:auto;">
                                @foreach($theLoais as $tl)
                                <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; padding:4px 6px; border-radius:4px; background:white;">
                                    <input type="checkbox" name="the_loai_ids[]" value="{{ $tl->id }}"
                                        {{ in_array($tl->id, old('the_loai_ids', [])) ? 'checked' : '' }}>
                                    {{ $tl->ten_the_loai }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── ĐIỀU KIỆN TÀI KHOẢN ── --}}
                    <div style="border-top:1px solid var(--color-border); margin:var(--space-4) 0; padding-top:var(--space-4);">
                        <div style="font-size:13px; font-weight:600; color:var(--color-text-muted); margin-bottom:var(--space-3); text-transform:uppercase; letter-spacing:0.5px;">👤 Điều kiện tài khoản</div>

                        <div class="form-group">
                            <select name="dieu_kien_tai_khoan" class="form-control">
                                <option value="" {{ old('dieu_kien_tai_khoan') == '' ? 'selected' : '' }}>Không giới hạn (ai cũng dùng được)</option>
                                <option value="new"      {{ old('dieu_kien_tai_khoan') == 'new'      ? 'selected' : '' }}>🆕 Tài khoản mới (đăng ký trong 30 ngày)</option>
                                <option value="verified" {{ old('dieu_kien_tai_khoan') == 'verified' ? 'selected' : '' }}>✅ Đã xác thực email</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Giá trị đơn hàng tối thiểu (VNĐ)</label>
                            <input type="number" name="don_hang_toi_thieu" class="form-control" min="0" step="1000" placeholder="Bỏ trống = không giới hạn" value="{{ old('don_hang_toi_thieu') }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tạo Mã Code</button>
                </form>
            </div>

            <div class="card" style="padding:var(--space-4); margin-top:var(--space-4); background:var(--color-bg-alt); border:1px dashed var(--color-border);">
                <div style="font-size:13px; color:var(--color-text-muted); margin-bottom:var(--space-3);">📄 Xuất file CSV hiện tại để dùng làm mẫu Import:</div>
                <a href="{{ route('admin.coupons.export') }}" class="btn btn-outline btn-sm" style="width:100%;">
                    <span class="material-icons" style="font-size:16px;">download</span> Xuất dữ liệu (.CSV)
                </a>
            </div>
        </div>

        {{-- ── DANH SÁCH MÃ ── --}}
        <div>
            <div class="card table-wrapper" style="overflow-x:auto;">
                <table class="table" style="min-width:640px;">
                    <thead>
                        <tr>
                            <th>Mã Code</th>
                            <th>Mức giảm</th>
                            <th style="min-width:140px;">Phạm vi / Điều kiện</th>
                            <th>Đã dùng / Tổng</th>
                            <th>Hạn &amp; Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $c)
                        <tr>
                            <td style="font-weight:var(--font-bold); color:var(--color-primary);">{{ $c->ma_code }}</td>
                            <td>
                                @if($c->loai == 'percent')
                                    <span class="badge badge-info">-{{ $c->gia_tri }}%</span>
                                @else
                                    <span class="badge badge-success">-{{ number_format($c->gia_tri, 0, ',', '.') }}đ</span>
                                @endif
                            </td>
                            <td>
                                {{-- Phạm vi --}}
                                @if($c->pham_vi === 'category')
                                    <span class="badge" style="background:#e8f4fd; color:#0c63e4; margin-bottom:3px;">📂 Thể loại</span>
                                    @if($c->the_loai_ids)
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">
                                        {{ \App\Models\TheLoai::whereIn('id',$c->the_loai_ids)->pluck('ten_the_loai')->implode(', ') }}
                                    </div>
                                    @endif
                                @elseif($c->pham_vi === 'book')
                                    <span class="badge" style="background:#fef3c7; color:#92400e; margin-bottom:3px;">📖 {{ count((array)$c->sach_ids) }} cuốn sách</span>
                                @else
                                    <span class="badge" style="background:#d1fae5; color:#065f46;">🌐 Tất cả</span>
                                @endif
                                {{-- Điều kiện tài khoản --}}
                                @if($c->dieu_kien_tai_khoan === 'new')
                                    <div style="font-size:11px; color:#856404; margin-top:2px;">🆕 Tài khoản mới</div>
                                @elseif($c->dieu_kien_tai_khoan === 'verified')
                                    <div style="font-size:11px; color:#065f46; margin-top:2px;">✅ Đã xác thực</div>
                                @endif
                                {{-- Đơn tối thiểu --}}
                                @if($c->don_hang_toi_thieu)
                                    <div style="font-size:11px; color:var(--color-text-muted); margin-top:2px;">Min: {{ number_format($c->don_hang_toi_thieu, 0, ',', '.') }}đ</div>
                                @endif
                            </td>
                            <td>{{ $c->da_dung }} / {{ $c->so_luong ? $c->so_luong : '∞' }}</td>
                            <td>
                                <div><span class="badge {{ $c->trang_thai ? 'badge-success' : 'badge-danger' }}">{{ $c->trang_thai ? 'Kích hoạt' : 'Đã tắt' }}</span></div>
                                @if($c->ngay_het_han)
                                    <div style="font-size:12px; color:var(--color-text-muted); margin-top:2px;">HSD: {{ $c->ngay_het_han->format('d/m/Y') }}
                                        @if($c->ngay_het_han < now()) <span style="color:var(--color-danger);">(Hết hạn)</span> @endif
                                    </div>
                                @else
                                    <div style="font-size:12px; color:var(--color-text-muted); margin-top:2px;">Vĩnh viễn</div>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:4px;">
                                    <form action="{{ route('admin.coupons.toggle', $c->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm btn-ghost" title="{{ $c->trang_thai ? 'Tắt' : 'Bật' }} mã">
                                            <span class="material-icons {{ $c->trang_thai ? 'text-warning' : 'text-success' }}">{{ $c->trang_thai ? 'pause' : 'play_arrow' }}</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Xóa mã {{ $c->ma_code }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-ghost"><span class="material-icons" style="color:var(--color-danger);">delete</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem;">Chưa có mã giảm giá nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
function togglePhamVi(val) {
    document.getElementById('group-category').style.display = (val === 'category') ? 'block' : 'none';
}
</script>
@endsection
