@extends('layouts.admin')

@section('title', 'Thêm thể loại')

@push('styles')
<style>
    .form-section { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); padding: var(--space-6); margin-bottom: var(--space-6); max-width: 640px; }
    .form-section:hover { box-shadow: var(--shadow-sm); }
    .form-section-title { font-size: var(--font-size-lg); font-weight: var(--font-semibold); color: var(--color-text); margin-bottom: var(--space-5); padding-bottom: var(--space-3); border-bottom: 1px solid var(--color-border-light); display: flex; align-items: center; gap: var(--space-2); }
    .form-section-title .material-icons { font-size: 20px; color: var(--color-primary); }
    .form-label .required { color: var(--color-danger); margin-left: 2px; }
    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); max-width: 640px; }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .alert-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .preview-group { padding: var(--space-3) var(--space-4); border-bottom: 1px solid var(--color-border-light); font-size: var(--font-size-sm); }
    .preview-group:last-child { border-bottom: none; }
    .preview-main { display: flex; align-items: center; gap: var(--space-3); }
    .preview-main .dot { width: 10px; height: 10px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); }
    .preview-main .name { font-weight: var(--font-semibold); }
    .preview-main .count { font-size: var(--font-size-xs); color: var(--color-text-muted); margin-left: auto; }
    .preview-sub { display: flex; align-items: center; gap: var(--space-2); padding: var(--space-2) 0 var(--space-2) var(--space-8); color: var(--color-text-secondary); }
    .preview-sub .sub-dot { width: 6px; height: 6px; border-radius: var(--radius-full); background: var(--color-primary); opacity: 0.4; }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <div>
            <a href="{{ route('admin.the-loai.index') }}" style="display: inline-flex; align-items: center; gap: var(--space-2); color: var(--color-text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--space-2); text-decoration: none;">
                <span class="material-icons" style="font-size: 18px;">arrow_back</span> Quản lý thể loại
            </a>
            <h1>Thêm thể loại</h1>
        </div>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <a href="{{ route('admin.the-loai.index') }}" class="btn btn-outline">
                <span class="material-icons" style="font-size: 18px;">close</span> Hủy
            </a>
            <button type="submit" form="add-the-loai-form" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">save</span> Lưu thể loại
            </button>
        </div>
    </div>

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
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form id="add-the-loai-form" action="{{ route('admin.the-loai.store') }}" method="POST">
        @csrf
        <div class="form-section">
            <div class="form-section-title">
                <span class="material-icons">category</span>
                Thông tin thể loại
            </div>

            <div class="form-group">
                <label class="form-label" for="ten_the_loai">Tên thể loại <span class="required">*</span></label>
                <input type="text" id="ten_the_loai" name="ten_the_loai" class="form-control @error('ten_the_loai') is-invalid @enderror"
                       placeholder="Nhập tên thể loại..." value="{{ old('ten_the_loai') }}" required maxlength="150" autofocus>
                @error('ten_the_loai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tối đa 150 ký tự. Ví dụ: Tiểu thuyết, Khoa học, Kinh doanh...</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="parent_id">Thuộc thể loại (tùy chọn)</label>
                <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                    <option value="">— Thể loại chính (không thuộc nhóm nào) —</option>
                    @foreach($theLoaiChas as $cha)
                        <option value="{{ $cha->id }}" {{ old('parent_id') == $cha->id ? 'selected' : '' }}>
                            {{ $cha->ten_the_loai }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Để trống nếu đây là thể loại chính. Chọn một thể loại nếu đây là thể loại phụ thuộc nhóm đó.</div>
            </div>
        </div>
    </form>

    {{-- Thể loại hiện có --}}
    @php
        $theLoaiTree = \App\Models\TheLoai::with('children')
                        ->whereNull('parent_id')
                        ->orderBy('ten_the_loai')
                        ->get();
    @endphp
    @if($theLoaiTree->count() > 0)
    <div class="form-section" style="max-width: 640px;">
        <div class="form-section-title">
            <span class="material-icons">list</span>
            Thể loại hiện có
        </div>
        @foreach($theLoaiTree as $parent)
            <div class="preview-group">
                <div class="preview-main">
                    <span class="dot"></span>
                    <span class="name">{{ $parent->ten_the_loai }}</span>
                    @if($parent->children->count() > 0)
                        <span class="count">{{ $parent->children->count() }} phụ</span>
                    @endif
                </div>
                @foreach($parent->children as $child)
                    <div class="preview-sub">
                        <span class="sub-dot"></span>
                        <span>{{ $child->ten_the_loai }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    @endif
@endsection
