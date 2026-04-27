@extends('layouts.admin')

@section('title', 'Quản lý đối tác')

@push('styles')
<style>
    .partner-tabs { display: flex; gap: var(--space-2); margin-bottom: var(--space-6); }
    .partner-tab { padding: var(--space-2) var(--space-5); border-radius: var(--radius-full); font-size: var(--font-size-sm); font-weight: var(--font-semibold); cursor: pointer; transition: all var(--transition-fast); border: 2px solid transparent; background: transparent; color: var(--color-text-secondary); font-family: var(--font-family); }
    .partner-tab.active { background: var(--color-primary); color: var(--color-text); border-color: var(--color-primary); }
    .partner-tab:not(.active):hover { background: var(--color-bg-alt); color: var(--color-text); }
    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-bottom: var(--space-6); }
    .stat-card { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); padding: var(--space-5); display: flex; align-items: center; gap: var(--space-4); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .stat-card .stat-icon.authors { background: #eff6ff; color: #2563eb; }
    .stat-card .stat-icon.publishers { background: #f0fdf4; color: #16a34a; }
    .stat-card .stat-icon.suppliers { background: #fef3c7; color: #d97706; }
    .stat-card .stat-value { font-size: var(--font-size-2xl); font-weight: var(--font-bold); }
    .stat-card .stat-label { font-size: var(--font-size-xs); color: var(--color-text-muted); }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý đối tác</h1>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            {{-- Search scoped to the currently-active tab via hidden `tab` field. --}}
            <form action="{{ route('admin.partners') }}" method="GET" class="header-search js-admin-search-form" style="max-width: 280px;">
                <input type="hidden" name="tab" value="tac-gia" class="js-admin-search-extra" id="partner-tab-input">
                <span class="material-icons search-icon">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Tìm kiếm..." id="partner-search" autocomplete="off">
                <span class="js-admin-search-spinner" aria-hidden="true"></span>
                <button type="button" class="js-admin-search-clear" aria-label="Xoá tìm kiếm" title="Xoá">
                    <span class="material-icons">close</span>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon authors"><span class="material-icons">person</span></div>
            <div>
                <div class="stat-value">{{ $tacGias->count() }}</div>
                <div class="stat-label">Tác giả</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon publishers"><span class="material-icons">business</span></div>
            <div>
                <div class="stat-value">{{ $nhaXuatBans->count() }}</div>
                <div class="stat-label">Nhà xuất bản</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon suppliers"><span class="material-icons">local_shipping</span></div>
            <div>
                <div class="stat-value">{{ $nhaCungCaps->count() }}</div>
                <div class="stat-label">Nhà cung cấp</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="partner-tabs" id="partner-tabs">
        <button class="partner-tab active" data-tab="tac-gia"
                data-create-url="{{ route('admin.tac-gia.create') }}"
                data-create-label="Thêm tác giả">
            <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">person</span>
            Tác giả ({{ $tacGias->count() }})
        </button>
        <button class="partner-tab" data-tab="nha-xuat-ban"
                data-create-url="{{ route('admin.nha-xuat-ban.create') }}"
                data-create-label="Thêm NXB">
            <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">business</span>
            NXB ({{ $nhaXuatBans->count() }})
        </button>
        <button class="partner-tab" data-tab="nha-cung-cap"
                data-create-url="{{ route('admin.nha-cung-cap.create') }}"
                data-create-label="Thêm NCC">
            <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">local_shipping</span>
            NCC ({{ $nhaCungCaps->count() }})
        </button>
    </div>

    {{-- Create button (href + label swap as tab changes) --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-4);">
        <a id="partner-create-btn" href="{{ route('admin.tac-gia.create') }}" class="btn btn-primary">
            <span class="material-icons" style="font-size: 18px;">add</span>
            <span id="partner-create-label">Thêm tác giả</span>
        </a>
    </div>

    {{-- Single AJAX target — rows swap on tab change or search. --}}
    <div class="js-admin-search-target" data-endpoint="{{ route('admin.partners') }}" style="position: relative;">
        <div class="table-wrapper card" style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox"></th>
                        <th>ID</th>
                        <th>Tên đối tác</th>
                        <th>Số sách</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody class="js-admin-search-rows">
                    @include('admin._partials.partners_rows', ['tab' => 'tac-gia', 'rows' => $tacGias])
                </tbody>
            </table>
        </div>
        <div class="js-admin-search-overlay" aria-hidden="true">
            <div class="js-admin-search-overlay__spinner"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const tabInput   = document.getElementById('partner-tab-input');
        const createBtn  = document.getElementById('partner-create-btn');
        const createLbl  = document.getElementById('partner-create-label');
        const searchForm = document.querySelector('.js-admin-search-form');

        document.querySelectorAll('.partner-tab').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.partner-tab').forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');

                // Sync hidden tab field, swap the create-CTA, re-run search.
                tabInput.value = tab.dataset.tab;
                if (createBtn && tab.dataset.createUrl) {
                    createBtn.href = tab.dataset.createUrl;
                }
                if (createLbl && tab.dataset.createLabel) {
                    createLbl.textContent = tab.dataset.createLabel;
                }

                // Fire a change event so the shared admin-search helper re-fetches.
                tabInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    })();
</script>
@endpush
