@extends('layouts.app')

@section('title', 'Thêm bài viết mới')

@push('styles')
<style>
    .blog-create-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    .blog-create-card {
        background: white;
        border-radius: var(--radius-xl);
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        padding: 2.5rem;
        border: 1px solid var(--color-border-light);
    }
    .blog-create-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .blog-create-header h1 {
        font-size: 2rem;
        color: var(--color-gray-900);
        margin-bottom: 0.5rem;
    }
    .blog-create-header p {
        color: var(--color-gray-500);
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--color-gray-700);
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--color-gray-300);
        border-radius: var(--radius-lg);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .form-control:focus {
        border-color: var(--color-primary-500);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px 12px;
    }
    .btn-submit {
        background: var(--color-primary-600);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: var(--radius-lg);
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }
    .btn-submit:hover {
        background: var(--color-primary-700);
    }
    .btn-submit:active {
        transform: scale(0.98);
    }
    .btn-cancel {
        color: var(--color-gray-600);
        text-decoration: none;
        font-weight: 500;
        padding: 0.875rem 1.5rem;
        border-radius: var(--radius-lg);
        transition: background 0.2s;
    }
    .btn-cancel:hover {
        background: var(--color-gray-100);
    }
    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        border: 1px solid #10b981;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .text-danger {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="blog-create-container">
    <div class="breadcrumb" style="margin-bottom: 1.5rem;">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span class="separator">›</span>
        <a href="{{ route('blog.index') }}">Blog</a>
        <span class="separator">›</span>
        <span>Thêm bài viết</span>
    </div>

    <div class="blog-create-card">
        <div class="blog-create-header">
            <h1>Tạo Bài Viết Mới</h1>
            <p>Chia sẻ kiến thức, review sách hoặc câu chuyện của bạn với cộng đồng Modtra Books</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <span class="material-icons">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="title" class="form-label">Tiêu đề bài viết</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required 
                           class="form-control" placeholder="Nhập tiêu đề hấp dẫn...">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category" class="form-label">Chủ đề</label>
                    <select id="category" name="category" required class="form-control form-select">
                        <option value="" disabled selected>-- Chọn chủ đề --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Ảnh bìa (Thumbnail)</label>
                <input type="file" id="image" name="image" accept="image/*" class="form-control" style="padding: 0.5rem 1rem;">
                <small style="color: var(--color-gray-500); display: block; margin-top: 0.25rem;">Khuyến nghị: Kích thước 800x400px, định dạng JPG/PNG, dung lượng < 2MB</small>
                @error('image')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="content" class="form-label">Nội dung bài viết</label>
                <textarea id="content" name="content" rows="15" placeholder="Viết nội dung bài viết của bạn...">{!! old('content') !!}</textarea>
                @error('content')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-gray-200);">
                <a href="{{ route('blog.index') }}" class="btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn-submit">
                    <span class="material-icons">send</span> Gửi Khám Duyệt
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#content',
                height: 500,
                plugins: 'image link media table lists code preview',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code preview',
                images_upload_url: '{{ route('blog.upload-image') }}',
                images_upload_credentials: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save(); // Đồng bộ nội dung với thẻ textarea
                    });
                }
            });
        }
    });
</script>
@endpush
