@extends('layouts.admin')

@section('title', 'Quản lý đối tác')

@push('styles')
<style>
    .partner-tabs { display: flex; gap: var(--space-2); margin-bottom: var(--space-6); }
    .partner-tab { padding: var(--space-2) var(--space-5); border-radius: var(--radius-full); font-size: var(--font-size-sm); font-weight: var(--font-semibold); cursor: pointer; transition: all var(--transition-fast); border: 2px solid transparent; background: transparent; color: var(--color-text-secondary); font-family: var(--font-family); }
    .partner-tab.active { background: var(--color-primary); color: var(--color-text); border-color: var(--color-primary); }
    .partner-tab:not(.active):hover { background: var(--color-bg-alt); color: var(--color-text); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-bottom: var(--space-6); }
    .stat-card { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); padding: var(--space-5); display: flex; align-items: center; gap: var(--space-4); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .stat-card .stat-icon.authors { background: #eff6ff; color: #2563eb; }
    .stat-card .stat-icon.publishers { background: #f0fdf4; color: #16a34a; }
    .stat-card .stat-icon.suppliers { background: #fef3c7; color: #d97706; }
    .stat-card .stat-value { font-size: var(--font-size-2xl); font-weight: var(--font-bold); }
    .stat-card .stat-label { font-size: var(--font-size-xs); color: var(--color-text-muted); }

    /* Inline edit form */
    .inline-edit-form { display: none; }
    .inline-edit-form.show { display: flex; align-items: center; gap: var(--space-2); }
    .inline-edit-name { display: flex; align-items: center; gap: var(--space-3); }
    .inline-edit-form .form-control { padding: var(--space-2) var(--space-3); font-size: var(--font-size-sm); }

    /* Modal overlay */
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-overlay.show { display: flex; }
    .modal-box { background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); max-width: 420px; width: 90%; box-shadow: var(--shadow-xl); }
    .modal-box h3 { margin-bottom: var(--space-4); }
    .modal-box .modal-actions { display: flex; gap: var(--space-3); justify-content: flex-end; margin-top: var(--space-5); }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>Quản lý đối tác</h1>
        <div style="display: flex; align-items: center; gap: var(--space-3);">
            <div class="header-search" style="max-width: 280px;">
                <span class="material-icons search-icon">search</span>
                <input type="text" placeholder="Tìm kiếm..." id="partner-search">
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <span class="material-icons" style="font-size: 20px;">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <span class="material-icons" style="font-size: 20px;">error</span>
            {{ session('error') }}
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
        <button class="partner-tab active" data-tab="tac-gia">
            <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">person</span>
            Tác giả ({{ $tacGias->count() }})
        </button>
        <button class="partner-tab" data-tab="nha-xuat-ban">
            <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">business</span>
            NXB ({{ $nhaXuatBans->count() }})
        </button>
        <button class="partner-tab" data-tab="nha-cung-cap">
            <span class="material-icons" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">local_shipping</span>
            NCC ({{ $nhaCungCaps->count() }})
        </button>
    </div>

    {{-- ═══ Tab: Tác giả ═══ --}}
    <div class="tab-content active" id="tab-tac-gia">
        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-4);">
            <a href="{{ route('admin.tac-gia.create') }}" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">add</span> Thêm tác giả
            </a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox"></th>
                        <th>ID</th>
                        <th>Tên tác giả</th>
                        <th>Số sách</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tacGias as $tg)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td style="color: var(--color-text-muted);">#{{ $tg->id }}</td>
                        <td>
                            {{-- Hiển thị tên (ẩn khi đang sửa) --}}
                            <div class="inline-edit-name" id="tg-name-{{ $tg->id }}">
                                <div style="width: 36px; height: 36px; background: #eff6ff; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span class="material-icons" style="font-size: 18px; color: #2563eb;">person</span>
                                </div>
                                <span style="font-weight: var(--font-medium);">{{ $tg->ten_tac_gia }}</span>
                            </div>
                            {{-- Form sửa inline --}}
                            <form class="inline-edit-form" id="tg-edit-{{ $tg->id }}" action="{{ route('admin.tac-gia.update', $tg->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="text" name="ten_tac_gia" class="form-control" value="{{ $tg->ten_tac_gia }}" style="max-width: 220px;" required>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="material-icons" style="font-size: 16px;">check</span>
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('tg', {{ $tg->id }})">
                                    <span class="material-icons" style="font-size: 16px;">close</span>
                                </button>
                            </form>
                        </td>
                        <td><span class="badge badge-primary">{{ $tg->sachs->count() }} sách</span></td>
                        <td style="color: var(--color-text-muted); font-size: var(--font-size-sm);">{{ $tg->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; gap: var(--space-1);">
                                <button class="btn btn-ghost btn-sm" title="Sửa" onclick="toggleEdit('tg', {{ $tg->id }})"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                                <form action="{{ route('admin.tac-gia.destroy', $tg->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tác giả &quot;{{ $tg->ten_tac_gia }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; padding: var(--space-8); color: var(--color-text-muted);">Chưa có tác giả nào. <a href="{{ route('admin.tac-gia.create') }}">Thêm ngay!</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Tab: Nhà xuất bản ═══ --}}
    <div class="tab-content" id="tab-nha-xuat-ban">
        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-4);">
            <a href="{{ route('admin.nha-xuat-ban.create') }}" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">add</span> Thêm NXB
            </a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox"></th>
                        <th>ID</th>
                        <th>Tên nhà xuất bản</th>
                        <th>Số sách</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nhaXuatBans as $nxb)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td style="color: var(--color-text-muted);">#{{ $nxb->id }}</td>
                        <td>
                            <div class="inline-edit-name" id="nxb-name-{{ $nxb->id }}">
                                <div style="width: 36px; height: 36px; background: #f0fdf4; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span class="material-icons" style="font-size: 18px; color: #16a34a;">business</span>
                                </div>
                                <span style="font-weight: var(--font-medium);">{{ $nxb->ten_nxb }}</span>
                            </div>
                            <form class="inline-edit-form" id="nxb-edit-{{ $nxb->id }}" action="{{ route('admin.nha-xuat-ban.update', $nxb->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="text" name="ten_nxb" class="form-control" value="{{ $nxb->ten_nxb }}" style="max-width: 220px;" required>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="material-icons" style="font-size: 16px;">check</span>
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('nxb', {{ $nxb->id }})">
                                    <span class="material-icons" style="font-size: 16px;">close</span>
                                </button>
                            </form>
                        </td>
                        <td><span class="badge badge-primary">{{ $nxb->sachs->count() }} sách</span></td>
                        <td style="color: var(--color-text-muted); font-size: var(--font-size-sm);">{{ $nxb->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; gap: var(--space-1);">
                                <button class="btn btn-ghost btn-sm" title="Sửa" onclick="toggleEdit('nxb', {{ $nxb->id }})"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                                <form action="{{ route('admin.nha-xuat-ban.destroy', $nxb->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa NXB &quot;{{ $nxb->ten_nxb }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; padding: var(--space-8); color: var(--color-text-muted);">Chưa có NXB nào. <a href="{{ route('admin.nha-xuat-ban.create') }}">Thêm ngay!</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Tab: Nhà cung cấp ═══ --}}
    <div class="tab-content" id="tab-nha-cung-cap">
        <div style="display: flex; justify-content: flex-end; margin-bottom: var(--space-4);">
            <a href="{{ route('admin.nha-cung-cap.create') }}" class="btn btn-primary">
                <span class="material-icons" style="font-size: 18px;">add</span> Thêm NCC
            </a>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox"></th>
                        <th>ID</th>
                        <th>Tên nhà cung cấp</th>
                        <th>Số sách</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nhaCungCaps as $ncc)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td style="color: var(--color-text-muted);">#{{ $ncc->id }}</td>
                        <td>
                            <div class="inline-edit-name" id="ncc-name-{{ $ncc->id }}">
                                <div style="width: 36px; height: 36px; background: #fef3c7; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <span class="material-icons" style="font-size: 18px; color: #d97706;">local_shipping</span>
                                </div>
                                <span style="font-weight: var(--font-medium);">{{ $ncc->ten_ncc }}</span>
                            </div>
                            <form class="inline-edit-form" id="ncc-edit-{{ $ncc->id }}" action="{{ route('admin.nha-cung-cap.update', $ncc->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="text" name="ten_ncc" class="form-control" value="{{ $ncc->ten_ncc }}" style="max-width: 220px;" required>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="material-icons" style="font-size: 16px;">check</span>
                                </button>
                                <button type="button" class="btn btn-ghost btn-sm" onclick="toggleEdit('ncc', {{ $ncc->id }})">
                                    <span class="material-icons" style="font-size: 16px;">close</span>
                                </button>
                            </form>
                        </td>
                        <td><span class="badge badge-primary">{{ $ncc->sachs->count() }} sách</span></td>
                        <td style="color: var(--color-text-muted); font-size: var(--font-size-sm);">{{ $ncc->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; gap: var(--space-1);">
                                <button class="btn btn-ghost btn-sm" title="Sửa" onclick="toggleEdit('ncc', {{ $ncc->id }})"><span class="material-icons" style="font-size: 18px;">edit</span></button>
                                <form action="{{ route('admin.nha-cung-cap.destroy', $ncc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa NCC &quot;{{ $ncc->ten_ncc }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" title="Xóa" style="color: var(--color-danger);"><span class="material-icons" style="font-size: 18px;">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; padding: var(--space-8); color: var(--color-text-muted);">Chưa có NCC nào. <a href="{{ route('admin.nha-cung-cap.create') }}">Thêm ngay!</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Tab switching
    document.querySelectorAll('.partner-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.partner-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        });
    });

    // Toggle inline edit
    function toggleEdit(prefix, id) {
        const nameEl = document.getElementById(prefix + '-name-' + id);
        const editEl = document.getElementById(prefix + '-edit-' + id);

        if (editEl.classList.contains('show')) {
            nameEl.style.display = '';
            editEl.classList.remove('show');
        } else {
            nameEl.style.display = 'none';
            editEl.classList.add('show');
            editEl.querySelector('input[type="text"]').focus();
        }
    }
</script>
@endpush
