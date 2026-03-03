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
        <a href="{{ url('/admin/inventory') }}" class="{{ request()->is('admin/inventory*') ? 'active' : '' }}" id="nav-inventory">
            <span class="material-icons">inventory_2</span>
            Quản lý sách
        </a>
        <a href="{{ url('/admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}" id="nav-orders">
            <span class="material-icons">receipt_long</span>
            Đơn hàng
        </a>
        <a href="{{ url('/admin/users') }}" id="nav-users">
            <span class="material-icons">group</span>
            Khách hàng
        </a>
        <a href="{{ url('/admin/blog') }}" id="nav-blog">
            <span class="material-icons">article</span>
            Blog
        </a>
        <a href="{{ url('/admin/settings') }}" id="nav-settings">
            <span class="material-icons">settings</span>
            Cài đặt
        </a>
    </nav>
</aside>
