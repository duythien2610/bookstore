@extends('layouts.app')

@section('title', 'Thêm bài viết mới')

@push('styles')
<style>
/* ── Layout ── */
.create-layout {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.5rem 4rem;
}
.create-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    align-items: start;
}
/* ── Cards ── */
.editor-card, .sidebar-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px 0 rgba(0,0,0,.06), 0 4px 16px -4px rgba(0,0,0,.08);
    overflow: hidden;
}
.card-head {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: .75rem;
}
.card-head-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.card-head-icon.blue  { background: #eff6ff; color: #2563eb; }
.card-head-icon.green { background: #f0fdf4; color: #16a34a; }
.card-head-icon.amber { background: #fffbeb; color: #d97706; }
.card-head h3 { font-size: .95rem; font-weight: 700; color: #111827; margin: 0; }
.card-head span.sub { font-size: .8rem; color: #6b7280; }

.card-body { padding: 1.5rem; }

/* ── Form elements ── */
.field { margin-bottom: 1.25rem; }
.field label {
    display: block;
    font-size: .875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: .5rem;
}
.field label .req { color: #ef4444; }
.field input[type=text],
.field select,
.field textarea {
    width: 100%;
    padding: .7rem 1rem;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    font-size: .925rem;
    color: #111827;
    font-family: 'Inter', sans-serif;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
    box-sizing: border-box;
}
.field input:focus,
.field select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}
.field .form-hint { font-size: .78rem; color: #9ca3af; margin-top: .35rem; }
.field .err { font-size: .8rem; color: #ef4444; margin-top: .35rem; }

/* ── Cover image upload ── */
.cover-dropzone {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: #f9fafb;
    position: relative;
}
.cover-dropzone:hover, .cover-dropzone.drag-over {
    border-color: #2563eb;
    background: #eff6ff;
}
.cover-dropzone input[type=file] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.cover-dropzone-icon { font-size: 2.5rem; color: #9ca3af; line-height: 1; }
.cover-dropzone p { margin: .5rem 0 .25rem; font-size: .9rem; font-weight: 600; color: #374151; }
.cover-dropzone small { font-size: .78rem; color: #9ca3af; }
.cover-preview {
    display: none;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background: #000;
}
.cover-preview img {
    width: 100%; height: 180px; object-fit: cover; display: block; opacity: .9;
}
.cover-preview-remove {
    position: absolute; top: 8px; right: 8px;
    background: rgba(0,0,0,.55);
    border: none; cursor: pointer;
    border-radius: 50%;
    width: 30px; height: 30px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 16px;
    transition: background .2s;
}
.cover-preview-remove:hover { background: rgba(220,38,38,.8); }

/* ── Status badge ── */
.info-badge {
    display: flex; align-items: flex-start; gap: .6rem;
    background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
    padding: 1rem; font-size: .82rem; color: #92400e;
}
.info-badge .material-icons { font-size: 18px; color: #d97706; flex-shrink: 0; margin-top: 1px; }

/* ── Action buttons ── */
.action-bar {
    display: flex; flex-direction: column; gap: .75rem;
}
.btn-publish {
    width: 100%;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white; border: none;
    padding: .85rem 1rem;
    border-radius: 12px;
    font-size: .95rem; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    transition: opacity .2s, transform .1s;
    box-shadow: 0 4px 12px rgba(37,99,235,.3);
}
.btn-publish:hover { opacity: .92; }
.btn-publish:active { transform: scale(.98); }
.btn-cancel-link {
    width: 100%;
    display: flex; align-items: center; justify-content: center; gap: .4rem;
    text-decoration: none;
    color: #6b7280; font-size: .875rem; font-weight: 500;
    padding: .7rem;
    border-radius: 10px;
    border: 1.5px solid #e5e7eb;
    transition: background .2s, color .2s;
}
.btn-cancel-link:hover { background: #f3f4f6; color: #374151; }

/* ── Alert ── */
.alert-success {
    background: #f0fdf4; border: 1px solid #86efac; color: #166534;
    padding: 1rem 1.25rem; border-radius: 12px;
    display: flex; align-items: center; gap: .75rem; margin-bottom: 1.5rem;
    font-size: .9rem; font-weight: 500;
}
.alert-error {
    background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b;
    padding: 1rem 1.25rem; border-radius: 12px;
    margin-bottom: 1.5rem; font-size: .875rem;
}
.alert-error ul { margin: .5rem 0 0; padding-left: 1.2rem; }

/* ── Cat pills ── */
.cat-pills { display: flex; flex-wrap: wrap; gap: .5rem; }
.cat-pill { display: none; }
.cat-pill + label {
    padding: .4rem .85rem;
    border-radius: 20px;
    border: 1.5px solid #e5e7eb;
    font-size: .82rem; font-weight: 600; color: #374151;
    cursor: pointer;
    transition: border-color .15s, background .15s, color .15s;
}
.cat-pill:checked + label {
    border-color: #2563eb;
    background: #eff6ff;
    color: #1d4ed8;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .create-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="create-layout">
    {{-- Breadcrumb --}}
    <div class="breadcrumb" style="margin-bottom:1.5rem">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="separator">›</span>
        <a href="{{ route('blog.index') }}">Blog</a>
        <span class="separator">›</span>
        <span>Tạo bài viết</span>
    </div>

    {{-- Page heading --}}
    <div style="margin-bottom:1.75rem">
        <h1 style="font-size:1.75rem;font-weight:800;color:#111827;margin:0 0 .3rem">✍️ Tạo bài viết mới</h1>
        <p style="color:#6b7280;font-size:.95rem;margin:0">Chia sẻ kiến thức, review sách hoặc câu chuyện của bạn với cộng đồng Modtra</p>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <span class="material-icons">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <strong>Có lỗi xảy ra, vui lòng kiểm tra lại:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data" id="blog-form">
        @csrf
        <div class="create-grid">

            {{-- ── LEFT: Main content ── --}}
            <div style="display:flex;flex-direction:column;gap:1.25rem">

                {{-- Tiêu đề --}}
                <div class="editor-card">
                    <div class="card-head">
                        <div class="card-head-icon blue"><span class="material-icons">title</span></div>
                        <div><h3>Thông tin cơ bản</h3><span class="sub">Tiêu đề & chủ đề bài viết</span></div>
                    </div>
                    <div class="card-body">
                        <div class="field">
                            <label for="title">Tiêu đề <span class="req">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}"
                                   placeholder="Nhập tiêu đề hấp dẫn…" required maxlength="255">
                            <div class="form-hint">Tối đa 255 ký tự — tiêu đề rõ ràng giúp người đọc tìm thấy bài viết dễ hơn</div>
                            @error('title')<div class="err">{{ $message }}</div>@enderror
                        </div>

                        {{-- Category pills --}}
                        <div class="field" style="margin-bottom:0">
                            <label>Chủ đề <span class="req">*</span></label>
                            <div class="cat-pills">
                                @foreach($categories as $cat)
                                    <input type="radio" class="cat-pill" name="category" id="cat-{{ Str::slug($cat) }}"
                                           value="{{ $cat }}" {{ old('category') == $cat ? 'checked' : '' }} required>
                                    <label for="cat-{{ Str::slug($cat) }}">{{ $cat }}</label>
                                @endforeach
                            </div>
                            @error('category')<div class="err" style="margin-top:.4rem">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Editor --}}
                <div class="editor-card">
                    <div class="card-head">
                        <div class="card-head-icon blue"><span class="material-icons">edit_note</span></div>
                        <div><h3>Nội dung bài viết</h3><span class="sub">Sử dụng thanh công cụ để định dạng văn bản, chèn ảnh&hellip;</span></div>
                    </div>
                    <div class="card-body" style="padding-bottom:0">
                        <textarea id="content" name="content" rows="18"
                                  placeholder="Bắt đầu viết nội dung…">{!! old('content') !!}</textarea>
                        @error('content')<div class="err" style="padding:0 0 1rem">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- ── RIGHT: Sidebar ── --}}
            <div style="display:flex;flex-direction:column;gap:1.25rem;position:sticky;top:90px">

                {{-- Đăng bài --}}
                <div class="sidebar-card">
                    <div class="card-head">
                        <div class="card-head-icon green"><span class="material-icons">rocket_launch</span></div>
                        <div><h3>Đăng bài</h3></div>
                    </div>
                    <div class="card-body">
                        <div class="info-badge" style="margin-bottom:1rem">
                            <span class="material-icons">info</span>
                            <span>Bài viết sẽ được <strong>Admin xem xét</strong> trước khi xuất bản công khai.</span>
                        </div>
                        <div class="action-bar">
                            <button type="submit" class="btn-publish" id="btn-submit">
                                <span class="material-icons">send</span>
                                Gửi xét duyệt
                            </button>
                            <a href="{{ route('blog.index') }}" class="btn-cancel-link">
                                <span class="material-icons" style="font-size:16px">arrow_back</span>
                                Quay lại Blog
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Ảnh bìa --}}
                <div class="sidebar-card">
                    <div class="card-head">
                        <div class="card-head-icon amber"><span class="material-icons">image</span></div>
                        <div><h3>Ảnh bìa</h3><span class="sub">Hiển thị trên danh sách blog</span></div>
                    </div>
                    <div class="card-body">
                        {{-- Preview khi đã chọn ảnh --}}
                        <div class="cover-preview" id="cover-preview">
                            <img id="cover-preview-img" src="" alt="Preview">
                            <button type="button" class="cover-preview-remove" id="cover-remove-btn" title="Xoá ảnh">
                                <span class="material-icons" style="font-size:16px">close</span>
                            </button>
                        </div>

                        {{-- Dropzone --}}
                        <div class="cover-dropzone" id="cover-dropzone">
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp,image/gif">
                            <div class="cover-dropzone-icon">🖼️</div>
                            <p>Kéo & thả hoặc <span style="color:#2563eb;font-weight:700">chọn file</span></p>
                            <small>JPG · PNG · WEBP · max 2MB — khuyến nghị 800 × 400 px</small>
                        </div>

                        @error('image')<div class="err" style="margin-top:.5rem">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Tips --}}
                <div class="sidebar-card">
                    <div class="card-head">
                        <div class="card-head-icon blue"><span class="material-icons">lightbulb</span></div>
                        <div><h3>Mẹo viết hay</h3></div>
                    </div>
                    <div class="card-body" style="padding-top:1rem">
                        <ul style="margin:0;padding:0 0 0 1rem;font-size:.82rem;color:#6b7280;display:flex;flex-direction:column;gap:.5rem;list-style:disc">
                            <li>Tiêu đề nên từ <strong>8–12 từ</strong>, rõ ràng và hấp dẫn</li>
                            <li>Thêm ảnh bìa để bài viết nổi bật hơn</li>
                            <li>Chia bài thành các đoạn ngắn, dễ đọc</li>
                            <li>Trích dẫn sách bằng blockquote cho sinh động</li>
                            <li>Bài viết tối thiểu <strong>300 từ</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── TinyMCE ──────────────────────────────────────────────────────────
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#content',
            height: 520,
            language: 'vi',
            plugins: 'image link media table lists code preview fullscreen wordcount emoticons',
            toolbar: [
                'undo redo | blocks fontsize | bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | bullist numlist | blockquote | link image media | emoticons | code fullscreen preview'
            ],
            menubar: false,
            branding: false,
            resize: true,
            min_height: 400,
            images_upload_url: '{{ route("blog.upload-image") }}',
            images_upload_credentials: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function(resolve, reject) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '{{ route("blog.upload-image") }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                    const fd = new FormData();
                    fd.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.upload.onprogress = function(e) { if (e.lengthComputable) progress(e.loaded / e.total * 100); };
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') { reject('Định dạng phản hồi không hợp lệ'); return; }
                            resolve(json.location);
                        } else {
                            reject('Upload thất bại: ' + xhr.status);
                        }
                    };
                    xhr.onerror = function() { reject('Lỗi kết nối!'); };
                    xhr.send(fd);
                });
            },
            content_style: "body { font-family: 'Inter', sans-serif; font-size: 16px; line-height: 1.7; color: #1f2937; padding: 1rem 1.5rem; } h1,h2,h3 { color: #111827; } blockquote { border-left: 4px solid #2563eb; margin: 1.5rem 0; padding: .75rem 1.25rem; background: #eff6ff; border-radius: 0 8px 8px 0; font-style: italic; }",
            setup: function(editor) {
                editor.on('change input', function() { editor.save(); });
            }
        });
    }

    // ── Cover image preview ───────────────────────────────────────────────
    const input      = document.getElementById('image');
    const dropzone   = document.getElementById('cover-dropzone');
    const preview    = document.getElementById('cover-preview');
    const previewImg = document.getElementById('cover-preview-img');
    const removeBtn  = document.getElementById('cover-remove-btn');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            dropzone.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    input.addEventListener('change', function() { if (this.files[0]) showPreview(this.files[0]); });

    removeBtn.addEventListener('click', function() {
        input.value = '';
        previewImg.src = '';
        preview.style.display = 'none';
        dropzone.style.display = 'block';
    });

    // Drag & drop
    dropzone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('drag-over'); });
    dropzone.addEventListener('dragleave', function() { this.classList.remove('drag-over'); });
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault(); this.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) { const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files; showPreview(file); }
    });

    // ── Submit guard ─────────────────────────────────────────────────────
    document.getElementById('blog-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-icons" style="animation:spin 1s linear infinite">sync</span> Đang gửi…';
    });
});
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush
