@extends('layouts.admin')

@section('title', 'Quản lý Blog')

@push('styles')
<style>
/* ── Stats strip ── */
.blog-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.75rem;
}
.stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    display: flex; align-items: center; gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.stat-icon.total  { background: #eff6ff; color: #2563eb; }
.stat-icon.pend   { background: #fffbeb; color: #d97706; }
.stat-icon.pub    { background: #f0fdf4; color: #16a34a; }
.stat-icon.rej    { background: #fef2f2; color: #dc2626; }
.stat-label { font-size: .75rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
.stat-value { font-size: 1.6rem; font-weight: 800; color: #111827; line-height: 1.1; }

/* ── Toolbar & filter ── */
.blog-toolbar {
    display: flex; align-items: center; gap: .75rem;
    margin-bottom: 1.25rem; flex-wrap: wrap;
}
.filter-pill {
    padding: .45rem .9rem;
    border-radius: 20px;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    font-size: .82rem; font-weight: 600; color: #374151;
    cursor: pointer; text-decoration: none;
    transition: border-color .15s, background .15s, color .15s;
}
.filter-pill:hover, .filter-pill.active {
    border-color: #2563eb; background: #eff6ff; color: #1d4ed8;
}

/* ── Table ── */
.blog-table-wrap {
    background: #fff; border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden;
}
.blog-table {
    width: 100%; border-collapse: collapse;
}
.blog-table thead tr { background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
.blog-table th {
    padding: .85rem 1rem;
    font-size: .78rem; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: .4px;
    text-align: left; white-space: nowrap;
}
.blog-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}
.blog-table tbody tr:last-child td { border-bottom: none; }
.blog-table tbody tr { transition: background .15s; }
.blog-table tbody tr:hover { background: #f9fafb; }

/* ── Post row ── */
.post-row { display: flex; align-items: center; gap: .85rem; }
.post-thumb {
    width: 64px; height: 48px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
    background: #f3f4f6;
}
.post-thumb-placeholder {
    width: 64px; height: 48px;
    border-radius: 8px;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #d1d5db; font-size: 22px;
}
.post-title {
    font-size: .9rem; font-weight: 600; color: #111827;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    line-height: 1.4;
}
.post-category {
    display: inline-block;
    font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    padding: .2rem .55rem;
    border-radius: 20px;
    background: #eff6ff; color: #2563eb;
    margin-top: .3rem;
}

/* ── Avatar ── */
.user-row { display: flex; align-items: center; gap: .6rem; }
.user-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    color: #fff; font-weight: 700; font-size: .8rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.user-name { font-size: .875rem; font-weight: 500; color: #374151; }

/* ── Status badges ── */
.badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .75rem;
    border-radius: 20px;
    font-size: .75rem; font-weight: 700; white-space: nowrap;
}
.badge-pending  { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.badge-published{ background: #f0fdf4; color: #166534; border: 1px solid #86efac; }
.badge-rejected { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
.badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* ── Action buttons ── */
.action-wrap { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; justify-content: flex-end; }
.btn-sm {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .4rem .8rem;
    border-radius: 8px; border: none; cursor: pointer;
    font-size: .78rem; font-weight: 700;
    text-decoration: none;
    transition: opacity .15s, transform .1s;
    white-space: nowrap;
}
.btn-sm:hover { opacity: .85; }
.btn-sm:active { transform: scale(.97); }
.btn-approve { background: #16a34a; color: #fff; }
.btn-reject  { background: #dc2626; color: #fff; }
.btn-view    { background: #2563eb; color: #fff; }
.btn-delete  { background: #111827; color: #fff; }

/* ── Alert ── */
.admin-alert {
    display: flex; align-items: center; gap: .75rem;
    padding: .9rem 1.25rem; border-radius: 12px;
    font-size: .875rem; font-weight: 500; margin-bottom: 1.5rem;
}
.admin-alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }

/* ── Empty ── */
.empty-state {
    text-align: center; padding: 4rem 2rem; color: #9ca3af;
}
.empty-state .material-icons { font-size: 3rem; display: block; margin-bottom: .75rem; }
</style>
@endpush

@section('content')

<div class="admin-header">
    <div class="header-title">
        <h1>📰 Quản lý Bài Viết Blog</h1>
        <p>Xét duyệt và quản lý các bài viết từ cộng đồng người dùng</p>
    </div>
</div>

@if(session('success'))
    <div class="admin-alert success">
        <span class="material-icons">check_circle</span>
        {{ session('success') }}
    </div>
@endif

{{-- ── Stats ── --}}
@php
    $total     = $posts->count();
    $pending   = $posts->where('status','pending')->count();
    $published = $posts->where('status','published')->count();
    $rejected  = $posts->where('status','rejected')->count();
@endphp
<div class="blog-stats">
    <div class="stat-card">
        <div class="stat-icon total"><span class="material-icons">article</span></div>
        <div><div class="stat-label">Tổng bài viết</div><div class="stat-value">{{ $total }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pend"><span class="material-icons">hourglass_top</span></div>
        <div><div class="stat-label">Chờ duyệt</div><div class="stat-value">{{ $pending }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pub"><span class="material-icons">check_circle</span></div>
        <div><div class="stat-label">Đã xuất bản</div><div class="stat-value">{{ $published }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon rej"><span class="material-icons">cancel</span></div>
        <div><div class="stat-label">Từ chối</div><div class="stat-value">{{ $rejected }}</div></div>
    </div>
</div>

{{-- ── Filter pills ── --}}
<div class="blog-toolbar">
    <span style="font-size:.85rem;font-weight:600;color:#374151">Lọc:</span>
    <a href="?status=" class="filter-pill {{ !request('status') ? 'active' : '' }}">Tất cả ({{ $total }})</a>
    <a href="?status=pending"   class="filter-pill {{ request('status') === 'pending'   ? 'active' : '' }}">⏳ Chờ duyệt ({{ $pending }})</a>
    <a href="?status=published" class="filter-pill {{ request('status') === 'published' ? 'active' : '' }}">✅ Đã xuất bản ({{ $published }})</a>
    <a href="?status=rejected"  class="filter-pill {{ request('status') === 'rejected'  ? 'active' : '' }}">❌ Từ chối ({{ $rejected }})</a>
</div>

{{-- ── Table ── --}}
<div class="blog-table-wrap">
    <table class="blog-table">
        <thead>
            <tr>
                <th>Bài viết</th>
                <th>Người đăng</th>
                <th>Ngày đăng</th>
                <th>Trạng thái</th>
                <th style="text-align:right">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @php
                $filtered = request('status') ? $posts->where('status', request('status')) : $posts;
            @endphp
            @forelse($filtered as $post)
            <tr>
                {{-- Post info --}}
                <td style="max-width:340px">
                    <div class="post-row">
                        @if($post->image)
                            <img src="{{ asset($post->image) }}" alt="" class="post-thumb">
                        @else
                            <div class="post-thumb-placeholder"><span class="material-icons">image</span></div>
                        @endif
                        <div>
                            <div class="post-title">{{ $post->title }}</div>
                            @if($post->category)
                                <span class="post-category">{{ $post->category }}</span>
                            @endif
                        </div>
                    </div>
                </td>

                {{-- Author --}}
                <td>
                    <div class="user-row">
                        <div class="user-avatar">{{ strtoupper(mb_substr($post->user->ho_ten ?? 'U', 0, 1)) }}</div>
                        <div class="user-name">{{ $post->user->ho_ten ?? 'Unknown' }}</div>
                    </div>
                </td>

                {{-- Date --}}
                <td style="font-size:.82rem;color:#6b7280;white-space:nowrap">
                    {{ $post->created_at->format('d/m/Y') }}<br>
                    <span style="color:#9ca3af">{{ $post->created_at->format('H:i') }}</span>
                </td>

                {{-- Status --}}
                <td>
                    @if($post->status === 'pending')
                        <span class="badge badge-pending"><span class="badge-dot"></span>Chờ duyệt</span>
                    @elseif($post->status === 'published')
                        <span class="badge badge-published"><span class="badge-dot"></span>Đã xuất bản</span>
                    @elseif($post->status === 'rejected')
                        <span class="badge badge-rejected"><span class="badge-dot"></span>Từ chối</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td>
                    <div class="action-wrap">
                        @if($post->status === 'pending')
                            <form action="{{ route('admin.blogs.approve', $post) }}" method="POST" style="margin:0">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-sm btn-approve">
                                    <span class="material-icons" style="font-size:14px">check</span> Duyệt
                                </button>
                            </form>
                            <form action="{{ route('admin.blogs.reject', $post) }}" method="POST" style="margin:0">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-sm btn-reject">
                                    <span class="material-icons" style="font-size:14px">close</span> Từ chối
                                </button>
                            </form>
                        @endif

                        @if($post->status === 'published')
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn-sm btn-view">
                                <span class="material-icons" style="font-size:14px">open_in_new</span> Xem
                            </a>
                        @endif

                        @if($post->status === 'rejected')
                            <form action="{{ route('admin.blogs.approve', $post) }}" method="POST" style="margin:0">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-sm btn-approve">
                                    <span class="material-icons" style="font-size:14px">refresh</span> Duyệt lại
                                </button>
                            </form>
                        @endif

                        {{-- Xoá bài viết (mọi trạng thái) --}}
                        <form action="{{ route('admin.blogs.destroy', $post) }}" method="POST" style="margin:0"
                              onsubmit="return confirm('Bạn có chắc muốn xoá VĨNH VIỄN bài viết &quot;{{ addslashes($post->title) }}&quot;?\n\nHành động này không thể hoàn tác!');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-delete">
                                <span class="material-icons" style="font-size:14px">delete</span> Xoá
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <span class="material-icons">article</span>
                        <p style="font-weight:600;color:#374151">Chưa có bài viết nào</p>
                        <p style="font-size:.85rem;margin:.25rem 0 0">Người dùng chưa gửi bài viết hoặc không có kết quả phù hợp với bộ lọc.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
