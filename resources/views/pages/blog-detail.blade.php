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
                <span class="badge badge-primary" style="margin-bottom: var(--space-4);">Review sách</span>
                <h1 style="font-size: var(--font-size-4xl); margin-bottom: var(--space-4);">10 cuốn sách thay đổi tư duy bạn nên đọc trong năm 2026</h1>
                <div style="display: flex; align-items: center; justify-content: center; gap: var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-muted);">
                    <div style="display: flex; align-items: center; gap: var(--space-2);">
                        <div class="author-avatar" style="width: 32px; height: 32px; font-size: var(--font-size-xs);">M</div>
                        <span>Modtra Books</span>
                    </div>
                    <span>·</span>
                    <span>01/03/2026</span>
                    <span>·</span>
                    <span>5 phút đọc</span>
                </div>
            </div>

            {{-- Featured Image --}}
            <div style="background: var(--color-bg-alt); border-radius: var(--radius-xl); height: 400px; display: flex; align-items: center; justify-content: center; margin-bottom: var(--space-8);">
                <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted);">image</span>
            </div>

            {{-- Article Content --}}
            <div style="font-size: var(--font-size-lg); line-height: 1.8; color: var(--color-text-secondary);">
                <p style="margin-bottom: var(--space-6);">Đọc sách không chỉ là một thói quen tốt mà còn là một hành trình khám phá bản thân. Mỗi cuốn sách mở ra một thế giới mới, giúp bạn nhìn nhận cuộc sống từ những góc độ khác nhau.</p>

                <h2 style="color: var(--color-text); margin: var(--space-8) 0 var(--space-4); font-size: var(--font-size-2xl);">1. Tư duy nhanh và chậm</h2>
                <p style="margin-bottom: var(--space-6);">Daniel Kahneman đã dành cả sự nghiệp để nghiên cứu về cách con người ra quyết định. Cuốn sách này sẽ giúp bạn hiểu rõ hơn về hai hệ thống tư duy trong não bộ.</p>

                <h2 style="color: var(--color-text); margin: var(--space-8) 0 var(--space-4); font-size: var(--font-size-2xl);">2. Sapiens: Lược sử loài người</h2>
                <p style="margin-bottom: var(--space-6);">Yuval Noah Harari đưa chúng ta vào một cuộc hành trình xuyên suốt lịch sử nhân loại, từ khi loài Homo sapiens xuất hiện đến thời đại công nghệ hiện đại.</p>

                <blockquote style="border-left: 4px solid var(--color-primary); padding: var(--space-4) var(--space-6); margin: var(--space-8) 0; background: var(--color-primary-light); border-radius: 0 var(--radius-lg) var(--radius-lg) 0; font-style: italic;">
                    "Một cuốn sách hay là cánh cửa mở ra thế giới mới, nơi trí tưởng tượng không có giới hạn."
                </blockquote>

                <h2 style="color: var(--color-text); margin: var(--space-8) 0 var(--space-4); font-size: var(--font-size-2xl);">3. Atomic Habits</h2>
                <p style="margin-bottom: var(--space-6);">James Clear chia sẻ những phương pháp khoa học để xây dựng thói quen tốt và loại bỏ thói quen xấu. Cuốn sách này đã thay đổi cuộc sống của hàng triệu người.</p>
            </div>

            {{-- Share --}}
            <div style="border-top: 1px solid var(--color-border-light); padding-top: var(--space-6); margin-top: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: var(--space-2);">
                    <span class="badge badge-primary">Sách hay</span>
                    <span class="badge badge-primary">Tư duy</span>
                    <span class="badge badge-primary">Review</span>
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
                @for ($i = 1; $i <= 3; $i++)
                <div class="card blog-card">
                    <div class="card-img" style="display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 48px; color: var(--color-text-muted);">article</span>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span>{{ rand(1, 28) }}/02/2026</span>
                            <span>·</span>
                            <span>{{ rand(3, 8) }} phút đọc</span>
                        </div>
                        <div class="card-title">Bài viết liên quan {{ $i }}</div>
                    </div>
                </div>
                @endfor
            </div>
        </section>
    </div>
@endsection
