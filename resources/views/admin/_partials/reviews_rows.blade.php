{{-- Table rows for Reviews. Shared between full-page render and AJAX
     search endpoint (Admin\ReviewController@index). --}}
@forelse($reviews as $r)
<tr>
    <td style="color: var(--color-text-muted); font-weight: var(--font-medium);">#{{ $r->id }}</td>

    {{-- Sách --}}
    <td>
        @if($r->sach)
            <div style="display:flex; align-items:center; gap:var(--space-3);">
                @php
                    $cover = $r->sach->link_anh_bia ?: ($r->sach->file_anh_bia ? asset('uploads/books/' . $r->sach->file_anh_bia) : null);
                @endphp
                @if($cover)
                    <img src="{{ $cover }}" alt="" style="width:36px; height:48px; object-fit:cover; border-radius:4px; flex-shrink:0;">
                @else
                    <div style="width:36px; height:48px; background:var(--color-bg-alt); border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <span class="material-icons" style="font-size:18px; color:var(--color-text-muted);">menu_book</span>
                    </div>
                @endif
                <div style="min-width:0;">
                    <div style="font-weight: var(--font-medium); font-size: 13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:200px;">{{ $r->sach->tieu_de }}</div>
                    <div style="font-size: 11px; color: var(--color-text-muted);">ID: {{ $r->sach->id }}</div>
                </div>
            </div>
        @else
            <span style="color: var(--color-text-muted); font-style: italic;">(sách đã xoá)</span>
        @endif
    </td>

    {{-- Người đánh giá --}}
    <td>
        @if($r->user)
            <div style="font-size: 13px; font-weight: var(--font-medium);">{{ $r->user->ho_ten ?? '—' }}</div>
            <div style="font-size: 11px; color: var(--color-text-muted);">{{ $r->user->email }}</div>
        @else
            <span style="color: var(--color-text-muted); font-style: italic;">(tài khoản đã xoá)</span>
        @endif
    </td>

    {{-- Số sao --}}
    <td>
        <div style="display:flex; align-items:center; gap:4px; color:#f59e0b;">
            @for($i = 1; $i <= 5; $i++)
                <span class="material-icons" style="font-size:16px;">{{ $i <= $r->so_sao ? 'star' : 'star_border' }}</span>
            @endfor
            <span style="font-size:12px; color:var(--color-text-muted); margin-left:4px;">({{ $r->so_sao }}/5)</span>
        </div>
    </td>

    {{-- Nội dung --}}
    <td style="max-width: 300px;">
        @if($r->tieu_de)
            <div style="font-size: 13px; font-weight: var(--font-semibold); color: var(--color-text); margin-bottom: 2px;">{{ $r->tieu_de }}</div>
        @endif
        @if($r->binh_luan)
            <div style="font-size: 12px; color: var(--color-text-secondary); line-height: 1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">{{ $r->binh_luan }}</div>
        @else
            <span style="font-size: 12px; color: var(--color-text-muted); font-style: italic;">(không có bình luận)</span>
        @endif
    </td>

    {{-- Trạng thái --}}
    <td>
        @if($r->trang_thai)
            <span class="badge badge-success">Hiển thị</span>
        @else
            <span class="badge badge-danger">Đã ẩn</span>
        @endif
    </td>

    {{-- Ngày tạo --}}
    <td style="color: var(--color-text-muted); font-size: 12px; white-space: nowrap;">
        {{ optional($r->created_at)->format('d/m/Y H:i') }}
    </td>

    {{-- Actions --}}
    <td>
        <div style="display: flex; gap: 4px;">
            <form action="{{ route('admin.reviews.toggle', $r->id) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-sm btn-ghost" title="{{ $r->trang_thai ? 'Ẩn đánh giá' : 'Hiển thị lại' }}">
                    <span class="material-icons" style="font-size:18px; color:{{ $r->trang_thai ? '#d97706' : '#16a34a' }};">{{ $r->trang_thai ? 'visibility_off' : 'visibility' }}</span>
                </button>
            </form>
            <form action="{{ route('admin.reviews.destroy', $r->id) }}" method="POST" style="display:inline;"
                  onsubmit="return confirm('Xoá vĩnh viễn đánh giá #{{ $r->id }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-ghost" title="Xoá">
                    <span class="material-icons" style="font-size:18px; color: var(--color-danger);">delete</span>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" style="text-align:center; padding:2rem; color:var(--color-text-muted);">
        <span class="material-icons" style="font-size:48px; display:block; margin-bottom:var(--space-3);">rate_review</span>
        @if(request('search'))
            Không tìm thấy đánh giá nào khớp với "<strong>{{ request('search') }}</strong>".
            <br>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline btn-sm" style="margin-top:var(--space-3);">Xoá tìm kiếm</a>
        @else
            Chưa có đánh giá nào.
        @endif
    </td>
</tr>
@endforelse

@if(isset($reviews) && method_exists($reviews, 'hasPages') && $reviews->hasPages())
<tr>
    <td colspan="8" style="padding: var(--space-4); background: var(--color-bg-alt);">
        <div style="display:flex; justify-content:center;">{{ $reviews->links() }}</div>
    </td>
</tr>
@endif
