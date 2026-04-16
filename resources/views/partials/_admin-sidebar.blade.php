{{-- Admin Sidebar Navigation --}}
<aside class="admin-sidebar" id="admin-sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">M</span>
        <span>Admin Panel</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}" id="nav-dashboard">
            <span class="material-icons">dashboard</span>
            Dashboard
        </a>
        <a href="{{ url('/admin/inventory') }}" class="{{ request()->is('admin/inventory*') || request()->is('admin/books*') ? 'active' : '' }}" id="nav-inventory">
            <span class="material-icons">inventory_2</span>
            Quản lý sách
        </a>
        <a href="{{ url('/admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}" id="nav-orders">
            <span class="material-icons">receipt_long</span>
            Đơn hàng
        </a>
        <a href="{{ route('admin.coupons.index') }}" class="{{ request()->is('admin/coupons*') ? 'active' : '' }}" id="nav-coupons">
            <span class="material-icons">local_offer</span>
            Mã giảm giá
        </a>

        {{-- Danh mục --}}
        <div style="padding: var(--space-3) var(--space-4) var(--space-1); font-size: 10px; font-weight: var(--font-semibold); color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-top: var(--space-2);">
            Danh mục
        </div>
        <a href="{{ route('admin.the-loai.index') }}" class="{{ request()->is('admin/the-loai*') ? 'active' : '' }}" id="nav-the-loai">
            <span class="material-icons">category</span>
            Thể loại
        </a>
        <a href="{{ route('admin.partners') }}" class="{{ request()->is('admin/partners*') || request()->is('admin/tac-gia*') || request()->is('admin/nha-xuat-ban*') || request()->is('admin/nha-cung-cap*') ? 'active' : '' }}" id="nav-partners">
            <span class="material-icons">handshake</span>
            Đối tác
        </a>

        {{-- Hệ thống --}}
        <div style="padding: var(--space-3) var(--space-4) var(--space-1); font-size: 10px; font-weight: var(--font-semibold); color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-top: var(--space-2);">
            Hệ thống
        </div>
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->is('admin/customers*') ? 'active' : '' }}" id="nav-customers">
            <span class="material-icons">group</span>
            Khách hàng
        </a>
        <a href="{{ route('admin.admins.index') }}" class="{{ request()->is('admin/admins*') ? 'active' : '' }}" id="nav-admins">
            <span class="material-icons">admin_panel_settings</span>
            Quản trị viên
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="{{ request()->is('admin/blogs*') ? 'active' : '' }}" id="nav-blog">
            <span class="material-icons">article</span>
            Blog
        </a>
        <a href="{{ route('admin.banners.index') }}" class="{{ request()->is('admin/banners*') ? 'active' : '' }}" id="nav-banners">
            <span class="material-icons">view_carousel</span>
            Quản lý Banner
        </a>
        <a href="{{ url('/admin/settings') }}" id="nav-settings">
            <span class="material-icons">settings</span>
            Cài đặt
        </a>
    </nav>
</aside>
