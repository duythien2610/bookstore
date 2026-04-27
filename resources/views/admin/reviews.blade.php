@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@push('styles')
<style>
    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

    .review-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-4); margin-bottom: var(--space-6); }
    .review-stat { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); padding: var(--space-5); display: flex; align-items: center; gap: var(--space-4); }
    .review-stat .icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .review-stat .icon.total    { background: #eff6ff; color: #2563eb; }
    .review-stat .icon.visible  { background: #dcfce7; color: #16a34a; }
    .review-stat .icon.hidden   { background: #fef3c7; color: #d97706; }
    .review-stat .icon.avg      { background: #fef2f2; color: #dc2626; }
    .review-stat .value { font-size: var(--font-size-2xl); font-weight: var(--font-bold); }
    .review-stat .label { font-size: var(--font-size-xs); color: var(--color-text-muted); }

    .star-select { height: 40px; padding: 0 var(--space-3); border: 1px solid var(--color-border); border-radius: var(--radius-md); background: var(--color-white); font-size: 13px; cursor: pointer; }

    @media (max-width: 900px) {
        .review-stats { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
    <div class="admin-topbar" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-3);">
        <h1>Quản lý đánh giá</h1>
        <div style="display: flex; align-items: center; gap: var(--space-3); flex-wrap:wrap;">
            {{-- Local search — matches Book/Coupon/Orders admin design. --}}
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="header-search js-admin-search-form" style="max-width: 320px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm theo sách, người dùng, nội dung..." id="review-search" autocomplete="off">
                <span class="js-admin-search-spinner" aria-hidden="true"></span>
                <button type="button" class="js-admin-search-clear" aria-label="Xoá tìm kiếm" title="Xoá">
                    <span class="material-icons">close</span>
                </button>
                {{-- Stars filter — participates in AJAX via `js-admin-search-extra`. --}}
                <select name="stars" class="star-select js-admin-search-extra" style="margin-left: var(--space-2);">
                    <option value="">Tất cả sao</option>
                    @foreach([5,4,3,2,1] as $s)
                        <option value="{{ $s }}" {{ (string)request('stars') === (string)$s ? 'selected' : '' }}>{{ $s }} sao</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stat cards --}}
    <div class="review-stats">
        <div class="review-stat">
            <div class="icon total"><span class="material-icons">rate_review</span></div>
            <div>
                <div class="value">{{ $stats['total'] }}</div>
                <div class="label">Tổng đánh giá</div>
            </div>
        </div>
        <div class="review-stat">
            <div class="icon visible"><span class="material-icons">visibility</span></div>
            <div>
                <div class="value">{{ $stats['visible'] }}</div>
                <div class="label">Đang hiển thị</div>
            </div>
        </div>
        <div class="review-stat">
            <div class="icon hidden"><span class="material-icons">visibility_off</span></div>
            <div>
                <div class="value">{{ $stats['hidden'] }}</div>
                <div class="label">Đang ẩn</div>
            </div>
        </div>
        <div class="review-stat">
            <div class="icon avg"><span class="material-icons">star</span></div>
            <div>
                <div class="value">{{ number_format($stats['avg'], 1) }}<span style="font-size:14px; color:var(--color-text-muted);"> / 5</span></div>
                <div class="label">Điểm trung bình</div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card table-wrapper js-admin-search-target"
         data-endpoint="{{ route('admin.reviews.index') }}"
         style="overflow-x: auto; position: relative;">
        <table class="table" style="min-width: 960px;">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th style="min-width: 220px;">Sách</th>
                    <th style="min-width: 180px;">Người đánh giá</th>
                    <th style="min-width: 140px;">Số sao</th>
                    <th>Nội dung</th>
                    <th style="width: 110px;">Trạng thái</th>
                    <th style="width: 130px;">Ngày gửi</th>
                    <th style="width: 110px;">Thao tác</th>
                </tr>
            </thead>
            <tbody class="js-admin-search-rows">
                @include('admin._partials.reviews_rows')
            </tbody>
        </table>
        <div class="js-admin-search-overlay" aria-hidden="true">
            <div class="js-admin-search-overlay__spinner"></div>
        </div>
    </div>
@endsection
