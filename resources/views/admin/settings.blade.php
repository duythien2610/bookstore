@extends('layouts.admin')

@section('title', 'Cài đặt')

@push('styles')
<style>
    .settings-grid { display: grid; grid-template-columns: 280px 1fr; gap: var(--space-6); }
    .settings-nav { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); overflow: hidden; height: fit-content; }
    .settings-nav a { display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4) var(--space-5); font-size: var(--font-size-sm); color: var(--color-text-secondary); transition: all var(--transition-fast); border-left: 3px solid transparent; text-decoration: none; }
    .settings-nav a:hover { background: var(--color-bg); color: var(--color-text); }
    .settings-nav a.active { background: var(--color-primary-light); color: var(--color-primary-dark); border-left-color: var(--color-primary); font-weight: var(--font-semibold); }
    .settings-nav a .material-icons { font-size: 20px; }
    .settings-panel { background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); }
    .settings-panel-header { padding: var(--space-6); border-bottom: 1px solid var(--color-border-light); }
    .settings-panel-header h3 { font-size: var(--font-size-lg); margin-bottom: var(--space-1); }
    .settings-panel-header p { font-size: var(--font-size-sm); color: var(--color-text-muted); }
    .settings-panel-body { padding: var(--space-6); }
    .settings-row { display: flex; align-items: flex-start; gap: var(--space-8); padding: var(--space-5) 0; border-bottom: 1px solid var(--color-border-light); }
    .settings-row:last-child { border-bottom: none; }
    .settings-row .label-col { flex: 0 0 180px; }
    .settings-row .label-col label { font-size: var(--font-size-sm); font-weight: var(--font-semibold); display: block; margin-bottom: var(--space-1); }
    .settings-row .label-col p { font-size: var(--font-size-xs); color: var(--color-text-muted); }
    .settings-row .input-col { flex: 1; }

    .alert { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); font-size: var(--font-size-sm); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-3); }
    .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

    .profile-avatar { width: 72px; height: 72px; border-radius: var(--radius-full); background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark)); display: flex; align-items: center; justify-content: center; color: white; font-size: var(--font-size-2xl); font-weight: var(--font-bold); }
    .info-card { background: var(--color-bg); border-radius: var(--radius-lg); padding: var(--space-4) var(--space-5); }
    .info-card .info-label { font-size: var(--font-size-xs); color: var(--color-text-muted); margin-bottom: var(--space-1); }
    .info-card .info-value { font-size: var(--font-size-sm); font-weight: var(--font-medium); }

    .overview-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-4); margin-top: var(--space-4); }
    .overview-stat { background: var(--color-bg); border-radius: var(--radius-lg); padding: var(--space-4); text-align: center; }
    .overview-stat .stat-number { font-size: var(--font-size-2xl); font-weight: var(--font-bold); color: var(--color-primary-dark); }
    .overview-stat .stat-text { font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: var(--space-1); }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    @media (max-width: 768px) {
        .settings-grid { grid-template-columns: 1fr; }
        .settings-row { flex-direction: column; gap: var(--space-3); }
        .settings-row .label-col { flex: none; }
        .overview-stats { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('content')
    <div class="admin-topbar">
        <h1>Cài đặt</h1>
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <span style="font-size: var(--font-size-sm); color: var(--color-text-muted);">
                Quản lý tài khoản & hệ thống
            </span>
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

    @if($errors->any())
        <div class="alert alert-error">
            <span class="material-icons" style="font-size: 20px;">error</span>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="settings-grid">
        {{-- Sidebar Navigation --}}
        <div class="settings-nav">
            <a href="#" class="active" data-settings-tab="account" id="settings-tab-account">
                <span class="material-icons">person</span>
                Thông tin tài khoản
            </a>
            <a href="#" data-settings-tab="password" id="settings-tab-password">
                <span class="material-icons">lock</span>
                Đổi mật khẩu
            </a>
            <a href="#" data-settings-tab="overview" id="settings-tab-overview">
                <span class="material-icons">bar_chart</span>
                Tổng quan hệ thống
            </a>
        </div>

        {{-- Content Panels --}}
        <div>
            {{-- ═══ Tab: Thông tin tài khoản ═══ --}}
            <div class="tab-panel active" id="panel-account">
                <div class="settings-panel">
                    <div class="settings-panel-header">
                        <h3>Thông tin tài khoản</h3>
                        <p>Xem thông tin tài khoản admin đang đăng nhập</p>
                    </div>
                    <div class="settings-panel-body">
                        <div style="display: flex; align-items: center; gap: var(--space-5); margin-bottom: var(--space-6); padding-bottom: var(--space-6); border-bottom: 1px solid var(--color-border-light);">
                            <div class="profile-avatar">
                                {{ strtoupper(substr($user->ho_ten ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size: var(--font-size-lg); font-weight: var(--font-bold);">{{ $user->ho_ten ?? 'Admin' }}</div>
                                <div style="font-size: var(--font-size-sm); color: var(--color-text-muted);">{{ $user->email ?? '' }}</div>
                                <span class="badge badge-success" style="margin-top: var(--space-2);">
                                    <span class="material-icons" style="font-size: 14px; margin-right: 4px;">verified</span>
                                    Quản trị viên
                                </span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-4);">
                            <div class="info-card">
                                <div class="info-label">Họ tên</div>
                                <div class="info-value">{{ $user->ho_ten ?? '—' }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email ?? '—' }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">Số điện thoại</div>
                                <div class="info-value">{{ $user->so_dien_thoai ?? '—' }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-label">Ngày tham gia</div>
                                <div class="info-value">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ Tab: Đổi mật khẩu ═══ --}}
            <div class="tab-panel" id="panel-password">
                <div class="settings-panel">
                    <div class="settings-panel-header">
                        <h3>Đổi mật khẩu</h3>
                        <p>Cập nhật mật khẩu bảo mật cho tài khoản admin</p>
                    </div>
                    <div class="settings-panel-body">
                        <form action="{{ route('admin.settings.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="settings-row">
                                <div class="label-col">
                                    <label for="current_password">Mật khẩu hiện tại</label>
                                    <p>Nhập mật khẩu đang sử dụng</p>
                                </div>
                                <div class="input-col">
                                    <input type="password" name="current_password" id="current_password"
                                           class="form-control" placeholder="Nhập mật khẩu hiện tại" required>
                                </div>
                            </div>

                            <div class="settings-row">
                                <div class="label-col">
                                    <label for="new_password">Mật khẩu mới</label>
                                    <p>Tối thiểu 6 ký tự</p>
                                </div>
                                <div class="input-col">
                                    <input type="password" name="new_password" id="new_password"
                                           class="form-control" placeholder="Nhập mật khẩu mới" required minlength="6">
                                </div>
                            </div>

                            <div class="settings-row">
                                <div class="label-col">
                                    <label for="new_password_confirmation">Xác nhận mật khẩu</label>
                                    <p>Nhập lại mật khẩu mới</p>
                                </div>
                                <div class="input-col">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                           class="form-control" placeholder="Xác nhận mật khẩu mới" required>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end; margin-top: var(--space-6);">
                                <button type="submit" class="btn btn-primary" id="btn-change-password">
                                    <span class="material-icons" style="font-size: 18px;">save</span>
                                    Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ═══ Tab: Tổng quan hệ thống ═══ --}}
            <div class="tab-panel" id="panel-overview">
                <div class="settings-panel">
                    <div class="settings-panel-header">
                        <h3>Tổng quan hệ thống</h3>
                        <p>Thông tin tổng quan về dữ liệu trong hệ thống</p>
                    </div>
                    <div class="settings-panel-body">
                        <div class="overview-stats">
                            <div class="overview-stat">
                                <div class="stat-number">{{ $tongSach }}</div>
                                <div class="stat-text">Tổng số sách</div>
                            </div>
                            <div class="overview-stat">
                                <div class="stat-number">{{ $tongDonHang }}</div>
                                <div class="stat-text">Đơn hàng</div>
                            </div>
                            <div class="overview-stat">
                                <div class="stat-number">{{ $tongKhachHang }}</div>
                                <div class="stat-text">Người dùng</div>
                            </div>
                            <div class="overview-stat">
                                <div class="stat-number">{{ $tongTheLoai }}</div>
                                <div class="stat-text">Thể loại</div>
                            </div>
                        </div>

                        <div style="margin-top: var(--space-6); padding-top: var(--space-6); border-top: 1px solid var(--color-border-light);">
                            <h4 style="margin-bottom: var(--space-4);">Thông tin hệ thống</h4>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-4);">
                                <div class="info-card">
                                    <div class="info-label">Framework</div>
                                    <div class="info-value">Laravel {{ app()->version() }}</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-label">PHP Version</div>
                                    <div class="info-value">{{ phpversion() }}</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-label">Môi trường</div>
                                    <div class="info-value">{{ ucfirst(app()->environment()) }}</div>
                                </div>
                                <div class="info-card">
                                    <div class="info-label">Thời gian server</div>
                                    <div class="info-value">{{ now()->format('d/m/Y H:i:s') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Settings tab switching
    document.querySelectorAll('[data-settings-tab]').forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();

            // Remove active from all tabs
            document.querySelectorAll('[data-settings-tab]').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

            // Add active to clicked tab
            tab.classList.add('active');
            document.getElementById('panel-' + tab.dataset.settingsTab).classList.add('active');
        });
    });
</script>
@endpush
