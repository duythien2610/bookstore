{{-- Site Header / Navigation --}}
<header class="site-header">
    <div class="container">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="site-logo" id="site-logo">
            <span class="logo-icon">M</span>
            <span>Modtra Books</span>
        </a>

        {{-- Navigation --}}
        <nav class="nav-links" id="main-nav">
            <a href="{{ url('/products') }}" class="{{ request()->is('products') ? 'active' : '' }}">Sách mới</a>

            <a href="{{ url('/products?view=categories') }}">Thể loại</a>
            <a href="{{ url('/blog') }}" class="{{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
            <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Liên hệ</a>
        </nav>

        {{-- Search --}}
        <div class="header-search" id="search-bar">
            <span class="material-icons search-icon">search</span>
            <input type="text" placeholder="Tìm kiếm sách, tác giả..." aria-label="Tìm kiếm">
        </div>

        {{-- Actions --}}
        <div class="header-actions">
            <a href="{{ url('/wishlist') }}" class="icon-btn" id="btn-wishlist" title="Danh sách yêu thích">
                <span class="material-icons">favorite_border</span>
            </a>
            <a href="{{ url('/cart') }}" class="icon-btn" id="btn-cart" title="Giỏ hàng">
                <span class="material-icons">shopping_cart</span>
            </a>
            @guest
                <a href="{{ url('/login') }}" class="btn btn-primary btn-sm" id="btn-login">Đăng nhập</a>
            @else
                <span style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-right: var(--space-2);">
                    Xin chào, <strong>{{ Auth::user()->ho_ten }}</strong>
                </span>
                <a href="{{ url('/profile') }}" class="icon-btn" id="btn-profile" title="Tài khoản">
                    <span class="material-icons">person</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="icon-btn" id="btn-logout" title="Đăng xuất" style="background: none; border: none; cursor: pointer;">
                        <span class="material-icons">logout</span>
                    </button>
                </form>
            @endguest
        </div>
    </div>
</header>
