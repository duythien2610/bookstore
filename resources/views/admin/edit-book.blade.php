@extends('layouts.admin')

@section('title', 'Chỉnh sửa: ' . $sach->tieu_de)

@push('styles')
<style>
    .add-book-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: var(--space-8);
    }

    .add-book-header .back-link {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        color: var(--color-text-secondary);
        font-size: var(--font-size-sm);
        font-weight: var(--font-medium);
        transition: color var(--transition-fast);
    }

    .add-book-header .back-link:hover {
        color: var(--color-primary-dark);
    }

    .add-book-header .header-actions {
        display: flex;
        align-items: center;
        gap: var(--space-3);
    }

    .add-book-form {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: var(--space-6);
        align-items: start;
    }

    .form-section {
        background: var(--color-white);
        border-radius: var(--radius-xl);
        border: 1px solid var(--color-border-light);
        padding: var(--space-6);
        margin-bottom: var(--space-6);
    }

    .form-section:hover {
        box-shadow: var(--shadow-sm);
    }

    .form-section-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-semibold);
        color: var(--color-text);
        margin-bottom: var(--space-5);
        padding-bottom: var(--space-3);
        border-bottom: 1px solid var(--color-border-light);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .form-section-title .material-icons {
        font-size: 20px;
        color: var(--color-primary);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-4);
    }

    .form-row-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }

    /* Cover Image Upload */
    .cover-upload-area {
        border: 2px dashed var(--color-border);
        border-radius: var(--radius-xl);
        padding: var(--space-8) var(--space-4);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-base);
        background: var(--color-bg);
        position: relative;
    }

    .cover-upload-area:hover {
        border-color: var(--color-primary);
        background: var(--color-primary-light);
    }

    .cover-upload-area .upload-icon {
        width: 56px;
        height: 56px;
        background: var(--color-primary-light);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto var(--space-4);
        color: var(--color-primary-dark);
        font-size: 28px;
        transition: transform var(--transition-base);
    }

    .cover-upload-area:hover .upload-icon {
        transform: scale(1.1);
    }

    .cover-upload-area h4 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-semibold);
        margin-bottom: var(--space-1);
        color: var(--color-text);
    }

    .cover-upload-area p {
        font-size: var(--font-size-xs);
        color: var(--color-text-muted);
    }

    .cover-upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Preview Image */
    .cover-preview {
        width: 100%;
        aspect-ratio: 3 / 4;
        border-radius: var(--radius-lg);
        object-fit: cover;
        display: none;
        margin-bottom: var(--space-3);
        border: 1px solid var(--color-border-light);
    }

    .cover-preview.active {
        display: block;
    }

    /* Required asterisk */
    .form-label .required {
        color: var(--color-danger);
        margin-left: 2px;
    }

    /* Alert messages */
    .alert {
        padding: var(--space-4) var(--space-5);
        border-radius: var(--radius-lg);
        font-size: var(--font-size-sm);
        margin-bottom: var(--space-6);
        display: flex;
        align-items: center;
        gap: var(--space-3);
    }

    .alert-success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Sidebar sticky */
    .add-book-sidebar {
        position: sticky;
        top: var(--space-6);
    }

    /* Current cover image */
    .current-cover {
        width: 100%;
        aspect-ratio: 3 / 4;
        border-radius: var(--radius-lg);
        object-fit: cover;
        margin-bottom: var(--space-3);
        border: 1px solid var(--color-border-light);
    }

    .current-cover-label {
        font-size: var(--font-size-xs);
        color: var(--color-text-muted);
        text-align: center;
        margin-bottom: var(--space-3);
    }

    @media (max-width: 1024px) {
        .add-book-form {
            grid-template-columns: 1fr;
        }
        .add-book-sidebar {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .form-row,
        .form-row-3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    {{-- Header --}}
    <div class="admin-topbar">
        <div>
            <a href="{{ route('admin.inventory') }}" class="back-link" style="display: inline-flex; align-items: center; gap: var(--space-2); color: var(--color-text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--space-2); text-decoration: none;">
                <span class="material-icons" style="font-size: 18px;">arrow_back</span> Quản lý sách
            </a>
            <h1>Chỉnh sửa sách</h1>
        </div>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <a href="{{ route('admin.inventory') }}" class="btn btn-outline">
                <span class="material-icons" style="font-size: 18px;">close</span> Hủy
            </a>
            <button type="submit" form="edit-book-form" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">save</span> Cập nhật
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <span class="material-icons" style="font-size: 20px;">error</span>
            <div>
                <strong>Vui lòng kiểm tra lại thông tin:</strong>
                <ul style="margin-top: var(--space-1); padding-left: var(--space-4); list-style: disc;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="edit-book-form" action="{{ route('admin.books.update', $sach->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="add-book-form">
            {{-- ═══════════ LEFT COLUMN ═══════════ --}}
            <div class="add-book-main">

                {{-- Thông tin cơ bản --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span class="material-icons">info</span>
                        Thông tin cơ bản
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tieu_de">Tên sách <span class="required">*</span></label>
                        <input type="text" id="tieu_de" name="tieu_de" class="form-control @error('tieu_de') is-invalid @enderror"
                               placeholder="Nhập tên sách..." value="{{ old('tieu_de', $sach->tieu_de) }}" required>
                        @error('tieu_de')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="isbn">Mã ISBN</label>
                            <input type="text" id="isbn" name="isbn" class="form-control @error('isbn') is-invalid @enderror"
                                   placeholder="Ví dụ: 978-604-..." value="{{ old('isbn', $sach->isbn) }}" maxlength="20">
                            @error('isbn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="loai_sach">Phân loại sách</label>
                            <select id="loai_sach" name="loai_sach" class="form-control @error('loai_sach') is-invalid @enderror">
                                <option value="trong_nuoc" {{ old('loai_sach', $sach->loai_sach) == 'trong_nuoc' ? 'selected' : '' }}>Sách trong nước</option>
                                <option value="nuoc_ngoai" {{ old('loai_sach', $sach->loai_sach) == 'nuoc_ngoai' ? 'selected' : '' }}>Sách nước ngoài</option>
                            </select>
                            @error('loai_sach')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="mo_ta">Mô tả sách</label>
                        <textarea id="mo_ta" name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror"
                                  placeholder="Nhập mô tả chi tiết về cuốn sách..." rows="5">{{ old('mo_ta', $sach->mo_ta) }}</textarea>
                        @error('mo_ta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Thông tin chi tiết --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span class="material-icons">menu_book</span>
                        Thông tin chi tiết
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="tac_gia_id">Tác giả</label>
                            <select id="tac_gia_id" name="tac_gia_id" class="form-control @error('tac_gia_id') is-invalid @enderror">
                                <option value="">— Chọn tác giả —</option>
                                @foreach($tacGias as $tg)
                                    <option value="{{ $tg->id }}" {{ old('tac_gia_id', $sach->tac_gia_id) == $tg->id ? 'selected' : '' }}>
                                        {{ $tg->ten_tac_gia }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tac_gia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="the_loai_id">Thể loại</label>
                            <select id="the_loai_id" name="the_loai_id" class="form-control @error('the_loai_id') is-invalid @enderror">
                                <option value="">— Chọn thể loại —</option>
                                @foreach($theLoais as $tl)
                                    @if($tl->children->count() > 0)
                                        <optgroup label="{{ $tl->ten_the_loai }}">
                                            @foreach($tl->children as $child)
                                                <option value="{{ $child->id }}" {{ old('the_loai_id', $sach->the_loai_id) == $child->id ? 'selected' : '' }}>
                                                    {{ $child->ten_the_loai }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @else
                                        @if(is_null($tl->parent_id))
                                            <option value="{{ $tl->id }}" {{ old('the_loai_id', $sach->the_loai_id) == $tl->id ? 'selected' : '' }}>
                                                {{ $tl->ten_the_loai }}
                                            </option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            @error('the_loai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="nha_xuat_ban_id">Nhà xuất bản</label>
                            <select id="nha_xuat_ban_id" name="nha_xuat_ban_id" class="form-control @error('nha_xuat_ban_id') is-invalid @enderror">
                                <option value="">— Chọn NXB —</option>
                                @foreach($nhaXuatBans as $nxb)
                                    <option value="{{ $nxb->id }}" {{ old('nha_xuat_ban_id', $sach->nha_xuat_ban_id) == $nxb->id ? 'selected' : '' }}>
                                        {{ $nxb->ten_nxb }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nha_xuat_ban_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nha_cung_cap_id">Nhà cung cấp</label>
                            <select id="nha_cung_cap_id" name="nha_cung_cap_id" class="form-control @error('nha_cung_cap_id') is-invalid @enderror">
                                <option value="">— Chọn NCC —</option>
                                @foreach($nhaCungCaps as $ncc)
                                    <option value="{{ $ncc->id }}" {{ old('nha_cung_cap_id', $sach->nha_cung_cap_id) == $ncc->id ? 'selected' : '' }}>
                                        {{ $ncc->ten_ncc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('nha_cung_cap_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row form-row-3">
                        <div class="form-group">
                            <label class="form-label" for="nam_xuat_ban">Năm xuất bản</label>
                            <input type="number" id="nam_xuat_ban" name="nam_xuat_ban" class="form-control @error('nam_xuat_ban') is-invalid @enderror"
                                   placeholder="2024" min="1900" max="{{ date('Y') }}" value="{{ old('nam_xuat_ban', $sach->nam_xuat_ban) }}">
                            @error('nam_xuat_ban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="so_trang">Số trang</label>
                            <input type="number" id="so_trang" name="so_trang" class="form-control @error('so_trang') is-invalid @enderror"
                                   placeholder="VD: 320" min="1" value="{{ old('so_trang', $sach->so_trang) }}">
                            @error('so_trang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="hinh_thuc_bia">Hình thức bìa</label>
                            <select id="hinh_thuc_bia" name="hinh_thuc_bia" class="form-control @error('hinh_thuc_bia') is-invalid @enderror">
                                <option value="">— Chọn —</option>
                                <option value="bia_mem" {{ old('hinh_thuc_bia', $sach->hinh_thuc_bia) == 'bia_mem' ? 'selected' : '' }}>Bìa mềm</option>
                                <option value="bia_cung" {{ old('hinh_thuc_bia', $sach->hinh_thuc_bia) == 'bia_cung' ? 'selected' : '' }}>Bìa cứng</option>
                                <option value="bia_gap" {{ old('hinh_thuc_bia', $sach->hinh_thuc_bia) == 'bia_gap' ? 'selected' : '' }}>Bìa gập</option>
                            </select>
                            @error('hinh_thuc_bia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Giá & Tồn kho --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span class="material-icons">payments</span>
                        Giá & Tồn kho
                    </div>

                    <div class="form-row form-row-3">
                        <div class="form-group">
                            <label class="form-label" for="gia_ban">Giá bán <span class="required">*</span></label>
                            <input type="number" id="gia_ban" name="gia_ban" class="form-control @error('gia_ban') is-invalid @enderror"
                                   placeholder="VD: 150000" min="0" step="1" value="{{ old('gia_ban', $sach->gia_ban) }}" required>
                            <div class="form-text">Đơn vị: VNĐ</div>
                            @error('gia_ban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="gia_goc">Giá gốc</label>
                            <input type="number" id="gia_goc" name="gia_goc" class="form-control @error('gia_goc') is-invalid @enderror"
                                   placeholder="VD: 200000" min="0" step="1" value="{{ old('gia_goc', $sach->gia_goc) }}">
                            <div class="form-text">Giá niêm yết (nếu có)</div>
                            @error('gia_goc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="so_luong_ton">Số lượng tồn <span class="required">*</span></label>
                            <input type="number" id="so_luong_ton" name="so_luong_ton" class="form-control @error('so_luong_ton') is-invalid @enderror"
                                   placeholder="VD: 100" min="0" value="{{ old('so_luong_ton', $sach->so_luong_ton) }}" required>
                            @error('so_luong_ton')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ RIGHT SIDEBAR ═══════════ --}}
            <div class="add-book-sidebar">

                {{-- Ảnh bìa --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <span class="material-icons">image</span>
                        Ảnh bìa sách
                    </div>

                    {{-- Hiển thị ảnh hiện tại --}}
                    @if($sach->file_anh_bia)
                        <img class="current-cover" src="{{ asset('uploads/books/' . $sach->file_anh_bia) }}" alt="Ảnh bìa hiện tại" id="current-cover">
                        <div class="current-cover-label">Ảnh bìa hiện tại</div>
                    @elseif($sach->link_anh_bia)
                        <img class="current-cover" src="{{ $sach->link_anh_bia }}" alt="Ảnh bìa hiện tại" id="current-cover">
                        <div class="current-cover-label">Ảnh bìa hiện tại</div>
                    @endif

                    <img id="cover-preview" class="cover-preview" src="" alt="Preview ảnh bìa mới">

                    <div class="cover-upload-area" id="cover-upload-area">
                        <input type="file" name="file_anh_bia" id="file_anh_bia" accept="image/*">
                        <div class="upload-icon">
                            <span class="material-icons">cloud_upload</span>
                        </div>
                        <h4>{{ $sach->file_anh_bia || $sach->link_anh_bia ? 'Thay đổi ảnh bìa' : 'Kéo thả hoặc nhấn để tải ảnh' }}</h4>
                        <p>PNG, JPG, WEBP — Tối đa 2MB</p>
                    </div>
                    @error('file_anh_bia')
                        <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                    @enderror

                    <div class="form-group" style="margin-top: var(--space-4);">
                        <label class="form-label" for="link_anh_bia">Hoặc nhập URL ảnh bìa</label>
                        <input type="url" id="link_anh_bia" name="link_anh_bia" class="form-control @error('link_anh_bia') is-invalid @enderror"
                               placeholder="https://example.com/image.jpg" value="{{ old('link_anh_bia', $sach->link_anh_bia) }}">
                        @error('link_anh_bia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Thông tin tạo --}}
                <div class="form-section" style="background: var(--color-bg); border-color: var(--color-border-light);">
                    <div class="form-section-title" style="border-bottom-color: var(--color-border-light);">
                        <span class="material-icons">schedule</span>
                        Thông tin
                    </div>
                    <div style="font-size: var(--font-size-sm); color: var(--color-text-secondary); display: flex; flex-direction: column; gap: var(--space-2);">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Ngày tạo:</span>
                            <span style="font-weight: var(--font-medium); color: var(--color-text);">{{ $sach->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Cập nhật:</span>
                            <span style="font-weight: var(--font-medium); color: var(--color-text);">{{ $sach->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Mã sách:</span>
                            <span style="font-weight: var(--font-medium); color: var(--color-text);">#{{ $sach->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Preview image upload
    const fileInput = document.getElementById('file_anh_bia');
    const preview = document.getElementById('cover-preview');
    const uploadArea = document.getElementById('cover-upload-area');
    const currentCover = document.getElementById('current-cover');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('active');
                    // Ẩn ảnh cũ khi chọn ảnh mới
                    if (currentCover) {
                        currentCover.style.display = 'none';
                        const label = document.querySelector('.current-cover-label');
                        if (label) label.style.display = 'none';
                    }
                    uploadArea.querySelector('h4').textContent = file.name;
                    uploadArea.querySelector('p').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Drag & Drop
    if (uploadArea) {
        ['dragenter', 'dragover'].forEach(event => {
            uploadArea.addEventListener(event, (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = 'var(--color-primary)';
                uploadArea.style.background = 'var(--color-primary-light)';
            });
        });

        ['dragleave', 'drop'].forEach(event => {
            uploadArea.addEventListener(event, (e) => {
                e.preventDefault();
                uploadArea.style.borderColor = '';
                uploadArea.style.background = '';
            });
        });

        uploadArea.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }
</script>
@endpush
