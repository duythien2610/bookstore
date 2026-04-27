{{-- Admin Sidebar Navigation — modernised v2 --}}
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/bookverse-logo.png') }}" alt="Bookverse" class="sidebar-logo__img">
        <div class="sidebar-logo__text">
            <span class="sidebar-logo__sub">Admin Console</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-group-label">Tổng quan</div>
        <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}" id="nav-dashboard">
            <span class="sidebar-nav__icon"><span class="material-icons">dashboard</span></span>
            <span>Dashboard</span>
        </a>

        <div class="sidebar-group-label">Bán hàng</div>
        <a href="{{ url('/admin/inventory') }}" class="{{ request()->is('admin/inventory*') || request()->is('admin/books*') ? 'active' : '' }}" id="nav-inventory">
            <span class="sidebar-nav__icon"><span class="material-icons">inventory_2</span></span>
            <span>Quản lý sách</span>
        </a>
        <a href="{{ url('/admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}" id="nav-orders">
            <span class="sidebar-nav__icon"><span class="material-icons">receipt_long</span></span>
            <span>Đơn hàng</span>
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="{{ request()->is('admin/coupons*') ? 'active' : '' }}" id="nav-coupons">
            <span class="sidebar-nav__icon"><span class="material-icons">local_offer</span></span>
            <span>Mã giảm giá</span>
        </a>

        <div class="sidebar-group-label">Danh mục</div>
        <a href="{{ route('admin.the-loai.index') }}" class="{{ request()->is('admin/the-loai*') ? 'active' : '' }}" id="nav-the-loai">
            <span class="sidebar-nav__icon"><span class="material-icons">category</span></span>
            <span>Thể loại</span>
        </a>
        <a href="{{ route('admin.partners') }}" class="{{ request()->is('admin/partners*') || request()->is('admin/tac-gia*') || request()->is('admin/nha-xuat-ban*') || request()->is('admin/nha-cung-cap*') ? 'active' : '' }}" id="nav-partners">
            <span class="sidebar-nav__icon"><span class="material-icons">handshake</span></span>
            <span>Đối tác</span>
        </a>

        <div class="sidebar-group-label">Hệ thống</div>
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->is('admin/customers*') ? 'active' : '' }}" id="nav-customers">
            <span class="sidebar-nav__icon"><span class="material-icons">group</span></span>
            <span>Khách hàng</span>
        </a>
        <a href="{{ route('admin.admins.index') }}" class="{{ request()->is('admin/admins*') ? 'active' : '' }}" id="nav-admins">
            <span class="sidebar-nav__icon"><span class="material-icons">admin_panel_settings</span></span>
            <span>Quản trị viên</span>
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="{{ request()->is('admin/blogs*') ? 'active' : '' }}" id="nav-blog">
            <span class="sidebar-nav__icon"><span class="material-icons">article</span></span>
            <span>Blog</span>
        </a>
        <a href="{{ route('admin.banners.index') }}" class="{{ request()->is('admin/banners*') ? 'active' : '' }}" id="nav-banners">
            <span class="sidebar-nav__icon"><span class="material-icons">view_carousel</span></span>
            <span>Banner</span>
        </a>
        <a href="{{ url('/admin/settings') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}" id="nav-settings">
            <span class="sidebar-nav__icon"><span class="material-icons">settings</span></span>
            <span>Cài đặt</span>
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->is('admin/reviews*') ? 'active' : '' }}" id="nav-reviews">
            <span class="sidebar-nav__icon"><span class="material-icons">rate_review</span></span>
            <span>Quản lý đánh giá</span>
        </a>
    </nav>

    <a href="{{ url('/') }}" class="sidebar-footer-card" target="_blank" rel="noopener">
        <span class="material-icons sidebar-footer-card__icon">storefront</span>
        <div class="sidebar-footer-card__text">
            <strong>Xem cửa hàng</strong>
            <span>Mở trang khách hàng</span>
        </div>
        <span class="material-icons sidebar-footer-card__arrow">north_east</span>
    </a>
</aside>
