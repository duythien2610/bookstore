{{-- Category groups list. Shared between full page render and AJAX
     search endpoint (TheLoaiController@index). --}}
@forelse($theLoaiChas as $parent)
<div class="category-group">
    <div class="category-group-header">
        <div class="info">
            <div class="icon-wrap">
                <span class="material-icons" style="font-size: 20px;">auto_stories</span>
            </div>
            <div>
                <div class="title">{{ $parent->ten_the_loai }}</div>
                <div class="meta">
                    {{ $parent->sachs->count() }} sách
                    @if($parent->children->count() > 0)
                        · {{ $parent->children->count() }} thể loại phụ
                    @endif
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 18px;">edit</span></button>
            <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
        </div>
    </div>

    @if($parent->children->count() > 0)
    <div class="sub-list">
        @foreach($parent->children as $child)
        <div class="sub-item">
            <div class="sub-info">
                <span class="sub-dot"></span>
                <span class="sub-name">{{ $child->ten_the_loai }}</span>
                <span class="sub-count">{{ $child->sachs->count() }} sách</span>
            </div>
            <div class="actions">
                <button class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 16px;">edit</span></button>
                <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 16px;">delete</span></button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@empty
<div style="text-align: center; padding: var(--space-12); color: var(--color-text-muted); background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light);">
    <span class="material-icons" style="font-size: 48px; margin-bottom: var(--space-4); display: block;">category</span>
    @if(request('search'))
        <p>Không tìm thấy thể loại nào khớp với "<strong>{{ request('search') }}</strong>".</p>
        <a href="{{ route('admin.the-loai.index') }}" class="btn btn-outline btn-sm" style="margin-top: var(--space-3);">Xóa tìm kiếm</a>
    @else
        <p>Chưa có thể loại nào.</p>
        <a href="{{ route('admin.the-loai.create') }}" class="btn btn-primary" style="margin-top: var(--space-4);">Thêm thể loại đầu tiên</a>
    @endif
</div>
@endforelse
