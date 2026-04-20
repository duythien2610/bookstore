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
            <h1>Blog Bookverse</h1>
        </div>
    </div>

    <div class="container">
        {{-- Featured Post (bài mới nhất) --}}
        @if($featuredPost)
        <a href="{{ route('blog.show', $featuredPost->slug) }}" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-8); margin-bottom: var(--space-12); background: var(--color-white); border-radius: var(--radius-xl); overflow: hidden; border: 1px solid var(--color-border-light); text-decoration: none; color: inherit; transition: box-shadow 0.2s;" id="featured-post" onmouseover="this.style.boxShadow='0 8px 32px rgba(0,0,0,0.10)'" onmouseout="this.style.boxShadow='none'">
            <div style="background: var(--color-bg-alt); display: flex; align-items: center; justify-content: center; min-height: 320px; overflow: hidden;">
                @if($featuredPost->image)
                    <img src="{{ asset($featuredPost->image) }}" alt="{{ $featuredPost->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted);">article</span>
                @endif
            </div>
            <div style="padding: var(--space-8); display: flex; flex-direction: column; justify-content: center;">
                <div style="display: flex; gap: var(--space-2); margin-bottom: var(--space-3);">
                    <span class="badge badge-primary">Nổi bật</span>
                    <span class="badge" style="background: var(--color-bg-alt); color: var(--color-text-secondary);">{{ $featuredPost->category }}</span>
                </div>
                <h2 style="margin-bottom: var(--space-3);">{{ $featuredPost->title }}</h2>
                <p style="color: var(--color-text-secondary); margin-bottom: var(--space-4); line-height: 1.7;">
                    {{ \Illuminate\Support\Str::limit(strip_tags($featuredPost->content), 160) }}
                </p>
                <div style="display: flex; align-items: center; gap: var(--space-3); font-size: var(--font-size-sm); color: var(--color-text-muted);">
                    <span>{{ $featuredPost->user->ho_ten ?? 'Bookverse' }}</span>
                    <span>·</span>
                    <span>{{ $featuredPost->created_at->format('d/m/Y') }}</span>
                    <span>·</span>
                    <span><span class="material-icons" style="font-size: 14px; vertical-align: -2px;">visibility</span> {{ number_format($featuredPost->views) }} lượt xem</span>
                </div>
            </div>
        </a>
        @endif

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
                        <span class="badge badge-primary" style="font-size: 10px;">{{ $post->user->ho_ten ?? 'Bookverse' }}</span>
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
