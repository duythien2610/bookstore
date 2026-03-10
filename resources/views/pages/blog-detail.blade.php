@extends('layouts.app')

@section('title', 'Bài viết blog')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <a href="{{ url('/blog') }}">Blog</a>
                <span class="separator">›</span>
                <span>Bài viết</span>
            </div>
        </div>
    </div>

    <div class="container">
        <article style="max-width: 800px; margin: 0 auto;" id="blog-article">
            {{-- Article Header --}}
            <div style="text-align: center; margin-bottom: var(--space-8);">
                <h1 style="font-size: var(--font-size-4xl); margin-bottom: var(--space-4);">{{ $post->title }}</h1>
                <div style="display: flex; align-items: center; justify-content: center; gap: var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-muted);">
                    <div style="display: flex; align-items: center; gap: var(--space-2);">
                        <div class="author-avatar" style="width: 32px; height: 32px; font-size: var(--font-size-xs);">{{ substr($post->user->ho_ten ?? 'M', 0, 1) }}</div>
                        <span>{{ $post->user->ho_ten ?? 'Modtra Books' }}</span>
                    </div>
                    <span>·</span>
                    <span>{{ $post->created_at->format('d/m/Y') }}</span>
                    
                    @if($post->status === 'pending')
                    <span>·</span>
                    <span style="color: #f39c12; font-weight: bold;">[Đang chờ duyệt]</span>
                    @endif
                </div>
            </div>

            {{-- Featured Image --}}
            @if($post->image)
            <div style="border-radius: var(--radius-xl); overflow: hidden; height: 400px; margin-bottom: var(--space-8);">
                <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif

            {{-- Article Content --}}
            <div class="blog-content-body" style="font-size: var(--font-size-lg); line-height: 1.8; color: var(--color-text-secondary);">
                {!! $post->content !!}
            </div>

            {{-- Share --}}
            <div style="border-top: 1px solid var(--color-border-light); padding-top: var(--space-6); margin-top: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: var(--space-2);">
                    <span class="badge badge-primary">{{ $post->category }}</span>
                </div>
                <div style="display: flex; gap: var(--space-2);">
                    <button class="btn btn-ghost btn-sm"><span class="material-icons" style="font-size: 18px;">share</span> Chia sẻ</button>
                    <button class="btn btn-ghost btn-sm"><span class="material-icons" style="font-size: 18px;">bookmark_border</span> Lưu</button>
                </div>
            </div>
        </article>

        {{-- Related Posts --}}
        <section class="section" id="related-posts">
            <div class="section-header">
                <h2>Bài viết liên quan</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-6);">
                @forelse ($relatedPosts as $related)
                <div class="card blog-card">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center; height: 200px; overflow: hidden; background: #f8f9fa;">
                        @if($related->image)
                            <img src="{{ asset($related->image) }}" alt="{{ $related->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span class="material-icons" style="font-size: 48px; color: var(--color-text-muted);">article</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span class="badge badge-primary" style="font-size: 10px;">{{ $related->category }}</span>
                            <span>{{ $related->created_at->format('d/m/Y') }}</span>
                        </div>
                        <a href="{{ route('blog.show', $related->slug) }}" style="text-decoration: none; color: inherit;">
                            <div class="card-title" style="margin-bottom: var(--space-2);">{{ \Illuminate\Support\Str::limit($related->title, 60) }}</div>
                        </a>
                        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); line-height: 1.6;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($related->content), 90) }}
                        </p>
                    </div>
                </div>
                @empty
                <div style="grid-column: span 3; text-align: center; padding: 2rem 0; color: var(--color-text-muted);">
                    <span class="material-icons" style="font-size: 36px; display: block; margin-bottom: 0.5rem;">article</span>
                    Chưa có bài viết liên quan.
                </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
