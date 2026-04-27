{{-- Table rows for Coupons. Shared between full page render and AJAX
     search endpoint (CouponController@index). --}}
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
    <td colspan="6" style="text-align:center; padding:2rem; color:var(--color-text-muted);">
        <span class="material-icons" style="font-size:48px; display:block; margin-bottom:var(--space-3);">local_offer</span>
        @if(request('search'))
            Không tìm thấy mã giảm giá nào khớp với "<strong>{{ request('search') }}</strong>".
            <br>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline btn-sm" style="margin-top:var(--space-3);">Xóa tìm kiếm</a>
        @else
            Chưa có mã giảm giá nào.
        @endif
    </td>
</tr>
@endforelse
