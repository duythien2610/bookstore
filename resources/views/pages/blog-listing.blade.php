@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Blog</span>
            </div>
            <h1>Blog Modtra Books</h1>
        </div>
    </div>

    <div class="container">
        {{-- Featured Post --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-8); margin-bottom: var(--space-12); background: var(--color-white); border-radius: var(--radius-xl); overflow: hidden; border: 1px solid var(--color-border-light);" id="featured-post">
            <div style="background: var(--color-bg-alt); display: flex; align-items: center; justify-content: center; min-height: 320px;">
                <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted);">article</span>
            </div>
            <div style="padding: var(--space-8); display: flex; flex-direction: column; justify-content: center;">
                <span class="badge badge-primary" style="width: fit-content; margin-bottom: var(--space-3);">Nổi bật</span>
                <h2 style="margin-bottom: var(--space-3);">10 cuốn sách thay đổi tư duy bạn nên đọc</h2>
                <p style="color: var(--color-text-secondary); margin-bottom: var(--space-4); line-height: 1.7;">Khám phá những cuốn sách kinh điển giúp bạn thay đổi cách nhìn về cuộc sống và công việc. Từ tâm lý học đến kinh doanh...</p>
                <div style="display: flex; align-items: center; gap: var(--space-3); font-size: var(--font-size-sm); color: var(--color-text-muted);">
                    <span>01/03/2026</span>
                    <span>·</span>
                    <span>5 phút đọc</span>
                </div>
            </div>
        </div>

        {{-- Category Filter --}}
        @php
            $categories = ['Tất cả', 'Review sách', 'Tác giả', 'Kiến thức', 'Sự kiện', 'Lifestyle', 'Khác'];
        @endphp
        
        <div style="display: flex; gap: var(--space-3); margin-bottom: var(--space-8); flex-wrap: wrap;" id="blog-categories">
            @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat]) }}" 
                   class="btn btn-sm {{ $currentCategory === $cat ? 'btn-primary' : 'btn-ghost' }}"
                   style="text-decoration: none;">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        {{-- Blog Grid --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem;">Tất cả bài viết</h2>
            @auth
            <a href="{{ route('blog.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <span class="material-icons">add</span> Viết bài mới
            </a>
            @endauth
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-6);" id="blog-grid">
            @forelse ($posts as $post)
            <div class="card blog-card">
                <div class="card-img" style="display: flex; align-items: center; justify-content: center; height: 200px; overflow: hidden; background: #f8f9fa;">
                    @if($post->image)
                        <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span class="material-icons" style="font-size: 48px; color: var(--color-text-muted);">article</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span class="badge badge-primary" style="font-size: 10px;">{{ $post->user->ho_ten ?? 'Modtra' }}</span>
                        <span>{{ $post->created_at->format('d/m/Y') }}</span>
                    </div>
                    <a href="{{ route('blog.show', $post->slug) }}" style="text-decoration: none; color: inherit;">
                        <div class="card-title" style="margin-bottom: var(--space-2);">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</div>
                    </a>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); line-height: 1.6;">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 100) }}
                    </p>
                </div>
            </div>
            @empty
            <div style="grid-column: span 3; text-align: center; padding: 4rem 0;">
                <span class="material-icons" style="font-size: 48px; color: #ccc;">article</span>
                <p style="margin-top: 1rem; color: #666;">Chưa có bài viết nào được đăng.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 3rem;">
            {{ $posts->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
