{{-- Table rows for the Book Management page.
     Extracted so the same markup is rendered by the full page AND by the
     AJAX search endpoint (SachController@index returns this as rendered HTML). --}}
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
