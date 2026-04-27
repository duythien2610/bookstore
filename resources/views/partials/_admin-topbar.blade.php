{{-- Admin global topbar — sticky, glassmorphic --}}
@php
    $adminUser = auth()->user();
    $adminName = $adminUser->ho_ten ?? ($adminUser->email ?? 'Quản trị viên');
    $adminInitials = collect(explode(' ', trim($adminName)))
        ->map(fn($part) => mb_substr($part, 0, 1))
        ->filter()
        ->take(2)
        ->implode('');
    $adminInitials = mb_strtoupper($adminInitials ?: 'A');
@endphp

<header class="admin-navbar">
    <div class="admin-navbar__left">
        <button class="admin-navbar__toggle" type="button" aria-label="Mở/Đóng sidebar" id="admin-sidebar-toggle">
            <span class="material-icons">menu</span>
        </button>
        <div class="admin-navbar__title">
            <span class="admin-navbar__eyebrow">Bookverse · Admin</span>
            <h1>@yield('page-title', View::getSection('title') ?? 'Bảng điều khiển')</h1>
        </div>
    </div>

    <div class="admin-navbar__right">
        <a href="{{ url('/') }}" class="admin-navbar__icon-btn" title="Xem cửa hàng">
            <span class="material-icons">storefront</span>
        </a>
        <button class="admin-navbar__icon-btn admin-navbar__icon-btn--badge" type="button" title="Thông báo">
            <span class="material-icons">notifications_none</span>
            <span class="admin-navbar__dot"></span>
        </button>

        <div class="admin-navbar__user" tabindex="0">
            <div class="admin-navbar__avatar">{{ $adminInitials }}</div>
            <div class="admin-navbar__user-info">
                <span class="admin-navbar__user-name">{{ $adminName }}</span>
                <span class="admin-navbar__user-role">Quản trị viên</span>
            </div>
            <span class="material-icons admin-navbar__caret">expand_more</span>

            <div class="admin-navbar__menu">
                <a href="{{ url('/admin/settings') }}" class="admin-navbar__menu-item">
                    <span class="material-icons">settings</span>
                    Cài đặt
                </a>
                <a href="{{ url('/') }}" class="admin-navbar__menu-item">
                    <span class="material-icons">storefront</span>
                    Về cửa hàng
                </a>
                <div class="admin-navbar__menu-sep"></div>
                <form method="POST" action="{{ route('logout') }}" class="admin-navbar__menu-form">
                    @csrf
                    <button type="submit" class="admin-navbar__menu-item admin-navbar__menu-item--danger">
                        <span class="material-icons">logout</span>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
