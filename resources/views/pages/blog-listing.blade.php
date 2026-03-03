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
        <div style="display: flex; gap: var(--space-3); margin-bottom: var(--space-8); flex-wrap: wrap;" id="blog-categories">
            <button class="btn btn-primary btn-sm">Tất cả</button>
            <button class="btn btn-ghost btn-sm">Review sách</button>
            <button class="btn btn-ghost btn-sm">Kiến thức</button>
            <button class="btn btn-ghost btn-sm">Tác giả</button>
            <button class="btn btn-ghost btn-sm">Sự kiện</button>
            <button class="btn btn-ghost btn-sm">Lifestyle</button>
        </div>

        {{-- Blog Grid --}}
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-6);" id="blog-grid">
            @for ($i = 1; $i <= 6; $i++)
            <div class="card blog-card" id="blog-post-{{ $i }}">
                <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                    <span class="material-icons" style="font-size: 48px; color: var(--color-text-muted);">article</span>
                </div>
                <div class="card-body">
                    <div class="card-meta">
                        <span class="badge badge-primary" style="font-size: 10px;">Review sách</span>
                        <span>{{ rand(1, 28) }}/02/2026</span>
                        <span>·</span>
                        <span>{{ rand(3, 10) }} phút đọc</span>
                    </div>
                    <div class="card-title" style="margin-bottom: var(--space-2);">Bài viết blog mẫu {{ $i }}</div>
                    <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); line-height: 1.6;">Mô tả ngắn gọn về bài viết blog, giúp thu hút người đọc vào nội dung chính...</p>
                </div>
            </div>
            @endfor
        </div>

        {{-- Pagination --}}
        <div class="pagination">
            <a href="#"><span class="material-icons">chevron_left</span></a>
            <span class="active">1</span>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#"><span class="material-icons">chevron_right</span></a>
        </div>
    </div>
@endsection
