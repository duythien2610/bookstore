@extends('layouts.admin')

@section('title', $pageTitle ?? 'Quản lý người dùng')

@push('styles')
<style>
    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .alert-error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-bottom: var(--space-6); }
    .stat-card { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); padding: var(--space-5); display: flex; align-items: center; gap: var(--space-4); }
    .stat-icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    .stat-icon.all     { background: #eff6ff; color: #2563eb; }
    .stat-icon.admin   { background: #fdf4ff; color: #9333ea; }
    .stat-icon.user    { background: #f0fdf4; color: #16a34a; }
    .stat-value { font-size: var(--font-size-2xl); font-weight: var(--font-bold); }
    .stat-label { font-size: var(--font-size-xs); color: var(--color-text-muted); }
    .filter-bar { display: flex; gap: var(--space-3); margin-bottom: var(--space-5); align-items: center; flex-wrap: wrap; }
    .badge-admin { background: #fdf4ff; color: #9333ea; padding: 2px 10px; border-radius: var(--radius-full); font-size: var(--font-size-xs); font-weight: var(--font-semibold); }
    .badge-user  { background: #f0fdf4; color: #16a34a; padding: 2px 10px; border-radius: var(--radius-full); font-size: var(--font-size-xs); font-weight: var(--font-semibold); }
    .avatar { width: 36px; height: 36px; background: linear-gradient(135deg, var(--color-primary), #8b5cf6); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; color: white; font-weight: var(--font-bold); font-size: var(--font-size-sm); flex-shrink: 0; }
    .role-select { font-size: var(--font-size-xs); padding: 4px 8px; border: 1px solid var(--color-border-light); border-radius: var(--radius-md); background: white; cursor: pointer; }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>{{ $pageTitle ?? 'Quản lý người dùng' }}</h1>
    </div>

    {{-- Flash messages --}}
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
            <div class="stat-icon all"><span class="material-icons">group</span></div>
            <div>
                <div class="stat-value">{{ $tongTatCa }}</div>
                <div class="stat-label">Tổng người dùng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon admin"><span class="material-icons">admin_panel_settings</span></div>
            <div>
                <div class="stat-value">{{ $tongAdmin }}</div>
                <div class="stat-label">Quản trị viên</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon user"><span class="material-icons">person</span></div>
            <div>
                <div class="stat-value">{{ $tongUser }}</div>
                <div class="stat-label">Khách hàng</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ request()->url() }}" class="filter-bar">
        <div class="header-search" style="flex: 1; max-width: 320px;">
            <span class="material-icons search-icon">search</span>
            <input type="text" name="search" placeholder="Tìm theo tên, email, SĐT..."
                   value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
        @if(request()->has('search'))
            <a href="{{ request()->url() }}" class="btn btn-ghost btn-sm">Xóa lọc</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color: var(--color-text-muted);">#{{ $user->id }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <div class="avatar">{{ mb_strtoupper(mb_substr($user->ho_ten, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight: var(--font-medium);">{{ $user->ho_ten }}</div>
                                @if($user->id === Auth::id())
                                    <span style="font-size: var(--font-size-xs); color: var(--color-text-muted);">(Bạn)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="color: var(--color-text-muted);">{{ $user->email }}</td>
                    <td style="color: var(--color-text-muted);">{{ $user->so_dien_thoai ?? '—' }}</td>
                    <td>
                        @if($user->role_id === 1)
                            <span class="badge-admin">Admin</span>
                        @else
                            <span class="badge-user">Khách hàng</span>
                        @endif
                    </td>
                    <td style="color: var(--color-text-muted); font-size: var(--font-size-sm);">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        <div style="display: flex; gap: var(--space-2); align-items: center;">
                            {{-- Đổi vai trò --}}
                            @if($user->id !== Auth::id())
                                <form method="POST" action="{{ route('admin.users.updateRole', $user) }}">
                                    @csrf @method('PUT')
                                    <select name="role_id" class="role-select"
                                            onchange="this.form.submit()" title="Đổi vai trò">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ $user->role_id === $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->ten_vai_tro) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                {{-- Xóa --}}
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Xóa người dùng \'{{ $user->ho_ten }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" title="Xóa"
                                            style="color: var(--color-danger);">
                                        <span class="material-icons" style="font-size: 18px;">delete</span>
                                    </button>
                                </form>
                            @else
                                <span style="font-size: var(--font-size-xs); color: var(--color-text-muted);">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: var(--space-8); color: var(--color-text-muted);">
                        Không tìm thấy người dùng nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
