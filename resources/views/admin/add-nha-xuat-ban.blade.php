@extends('layouts.admin')

@section('title', 'Thêm nhà xuất bản')

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
    .recent-item { display: flex; align-items: center; justify-content: space-between; padding: var(--space-3) var(--space-4); border-bottom: 1px solid var(--color-border-light); font-size: var(--font-size-sm); }
    .recent-item:last-child { border-bottom: none; }
    .recent-item .name { font-weight: var(--font-medium); }
    .recent-item .date { color: var(--color-text-muted); font-size: var(--font-size-xs); }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <div>
            <a href="{{ route('admin.inventory') }}" style="display: inline-flex; align-items: center; gap: var(--space-2); color: var(--color-text-secondary); font-size: var(--font-size-sm); margin-bottom: var(--space-2); text-decoration: none;">
                <span class="material-icons" style="font-size: 18px;">arrow_back</span> Quản lý danh mục
            </a>
            <h1>Thêm nhà xuất bản</h1>
        </div>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <a href="{{ route('admin.inventory') }}" class="btn btn-outline">
                <span class="material-icons" style="font-size: 18px;">close</span> Hủy
            </a>
            <button type="submit" form="add-nxb-form" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">save</span> Lưu NXB
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

    <form id="add-nxb-form" action="{{ route('admin.nha-xuat-ban.store') }}" method="POST">
        @csrf
        <div class="form-section">
            <div class="form-section-title">
                <span class="material-icons">business</span>
                Thông tin nhà xuất bản
            </div>

            <div class="form-group">
                <label class="form-label" for="ten_nxb">Tên nhà xuất bản <span class="required">*</span></label>
                <input type="text" id="ten_nxb" name="ten_nxb" class="form-control @error('ten_nxb') is-invalid @enderror"
                       placeholder="Nhập tên nhà xuất bản..." value="{{ old('ten_nxb') }}" required maxlength="150" autofocus>
                @error('ten_nxb')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tối đa 150 ký tự. Ví dụ: NXB Kim Đồng, NXB Trẻ, NXB Tổng hợp TP.HCM...</div>
            </div>
        </div>
    </form>

    @php
        $recentNxbs = \App\Models\NhaXuatBan::orderByDesc('created_at')->take(5)->get();
    @endphp
    @if($recentNxbs->count() > 0)
    <div class="form-section" style="max-width: 640px;">
        <div class="form-section-title">
            <span class="material-icons">history</span>
            NXB đã thêm gần đây
        </div>
        @foreach($recentNxbs as $nxb)
            <div class="recent-item">
                <span class="name">{{ $nxb->ten_nxb }}</span>
                <span class="date">{{ $nxb->created_at->format('d/m/Y H:i') }}</span>
            </div>
        @endforeach
    </div>
    @endif
@endsection
