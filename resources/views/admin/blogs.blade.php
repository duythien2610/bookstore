@extends('layouts.admin')

@section('title', 'Quản lý Blog')

@section('content')
<div class="admin-header">
    <div class="header-title">
        <h1>Quản lý Bài Viết Blog</h1>
        <p>Phê duyệt và quản lý các bài viết do người dùng đóng góp</p>
    </div>
</div>

@if(session('success'))
<div style="background: #e6f4ea; color: #1e8e3e; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
    {{ session('success') }}
</div>
@endif

<div class="admin-card">
    <div class="table-responsive">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Người đăng</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>#{{ $post->id }}</td>
                    <td style="font-weight: 500; color: var(--color-gray-900);">
                        {{ \Illuminate\Support\Str::limit($post->title, 50) }}
                    </td>
                    <td>{{ $post->user->ho_ten ?? 'Unknown' }}</td>
                    <td style="color: var(--color-gray-500);">
                        {{ $post->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @if($post->status === 'pending')
                            <span style="background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Chờ duyệt</span>
                        @elseif($post->status === 'published')
                            <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Đã xuất bản</span>
                        @elseif($post->status === 'rejected')
                            <span style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Từ chối</span>
                        @else
                            <span style="background: #e2e3e5; color: #383d41; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($post->status) }}</span>
                        @endif
                    </td>
                    <td style="text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                        {{-- Hiển thị nút Phê duyệt / Từ chối nếu đang ở trạng thái pending --}}
                        @if($post->status === 'pending')
                            <form action="{{ route('admin.blogs.approve', $post) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" style="background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">Duyệt</button>
                            </form>
                            <form action="{{ route('admin.blogs.reject', $post) }}" method="POST">
                                @csrf @method('PUT')
                                <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">Từ chối</button>
                            </form>
                        @endif

                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" style="background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none;">Xem thử</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--color-gray-500);">Chưa có bài viết nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
