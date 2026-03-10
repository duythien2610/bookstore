@extends('layouts.admin')

@section('title', 'Quản lý thể loại')

@push('styles')
<style>
    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .stat-row { display: flex; gap: var(--space-4); margin-bottom: var(--space-6); }
    .stat-item { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); padding: var(--space-5); flex: 1; display: flex; align-items: center; gap: var(--space-4); }
    .stat-item .icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .stat-item .icon.main { background: var(--color-primary-light); color: var(--color-primary-dark); }
    .stat-item .icon.sub { background: #eff6ff; color: #2563eb; }
    .stat-item .icon.total { background: #fef3c7; color: #d97706; }
    .stat-item .value { font-size: var(--font-size-2xl); font-weight: var(--font-bold); }
    .stat-item .label { font-size: var(--font-size-xs); color: var(--color-text-muted); }

    .category-group { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); margin-bottom: var(--space-4); overflow: hidden; transition: box-shadow var(--transition-fast); }
    .category-group:hover { box-shadow: var(--shadow-sm); }
    .category-group-header { display: flex; align-items: center; justify-content: space-between; padding: var(--space-5) var(--space-5); }
    .category-group-header .info { display: flex; align-items: center; gap: var(--space-3); }
    .category-group-header .icon-wrap { width: 40px; height: 40px; background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; color: white; }
    .category-group-header .title { font-weight: var(--font-semibold); font-size: var(--font-size-base); }
    .category-group-header .meta { font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: 2px; }
    .category-group-header .actions { display: flex; gap: var(--space-1); }

    .sub-list { border-top: 1px solid var(--color-border-light); padding: var(--space-3) var(--space-5); background: var(--color-bg); }
    .sub-item { display: flex; align-items: center; justify-content: space-between; padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); transition: background var(--transition-fast); }
    .sub-item:hover { background: var(--color-white); }
    .sub-item .sub-info { display: flex; align-items: center; gap: var(--space-3); }
    .sub-item .sub-dot { width: 8px; height: 8px; border-radius: var(--radius-full); background: var(--color-primary); opacity: 0.5; }
    .sub-item .sub-name { font-size: var(--font-size-sm); color: var(--color-text); }
    .sub-item .sub-count { font-size: var(--font-size-xs); color: var(--color-text-muted); }
    .sub-item .actions { display: flex; gap: var(--space-1); opacity: 0; transition: opacity var(--transition-fast); }
    .sub-item:hover .actions { opacity: 1; }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý thể loại</h1>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <div class="header-search" style="max-width: 280px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" placeholder="Tìm thể loại..." id="category-search">
            </div>
            <a href="{{ route('admin.the-loai.create') }}" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">add</span> Thêm thể loại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Thống kê --}}
    <div class="stat-row">
        <div class="stat-item">
            <div class="icon main"><span class="material-icons">auto_stories</span></div>
            <div>
                <div class="value">{{ $theLoaiChas->count() }}</div>
                <div class="label">Thể loại chính</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="icon sub"><span class="material-icons">style</span></div>
            <div>
                <div class="value">{{ $tongCon }}</div>
                <div class="label">Thể loại phụ</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="icon total"><span class="material-icons">category</span></div>
            <div>
                <div class="value">{{ $theLoaiChas->count() + $tongCon }}</div>
                <div class="label">Tổng thể loại</div>
            </div>
        </div>
    </div>

    {{-- Danh sách thể loại --}}
    @if($theLoaiChas->count() > 0)
        @foreach($theLoaiChas as $parent)
        <div class="category-group">
            <div class="category-group-header">
                <div class="info">
                    <div class="icon-wrap">
                        <span class="material-icons" style="font-size: 20px;">auto_stories</span>
                    </div>
                    <div>
                        <div class="title">{{ $parent->ten_the_loai }}</div>
                        <div class="meta">
                            {{ $parent->sachs->count() }} sách
                            @if($parent->children->count() > 0)
                                · {{ $parent->children->count() }} thể loại phụ
                            @endif
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                    <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
                </div>
            </div>

            @if($parent->children->count() > 0)
            <div class="sub-list">
                @foreach($parent->children as $child)
                <div class="sub-item">
                    <div class="sub-info">
                        <span class="sub-dot"></span>
                        <span class="sub-name">{{ $child->ten_the_loai }}</span>
                        <span class="sub-count">{{ $child->sachs->count() }} sách</span>
                    </div>
                    <div class="actions">
                        <button class="btn btn-ghost btn-sm" title="Sửa"><span class="material-icons" style="font-size: 16px;">edit</span></button>
                        <button class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 16px;">delete</span></button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    @else
    <div style="text-align: center; padding: var(--space-12); color: var(--color-text-muted); background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light);">
        <span class="material-icons" style="font-size: 48px; margin-bottom: var(--space-4); display: block;">category</span>
        <p>Chưa có thể loại nào.</p>
        <a href="{{ route('admin.the-loai.create') }}" class="btn btn-primary" style="margin-top: var(--space-4);">Thêm thể loại đầu tiên</a>
    </div>
    @endif
@endsection
