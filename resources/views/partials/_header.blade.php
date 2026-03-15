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
            <a href="{{ url('/products') }}" class="{{ request()->is('products') && !request()->has('loai_sach') ? 'active' : '' }}">Sách mới</a>

            {{-- Thể loại - Multilevel Dropdown --}}
            <div class="nav-dropdown">
                <a href="{{ url('/products') }}" class="nav-dropbtn {{ request()->has('loai_sach') || request()->has('the_loai_id') ? 'active' : '' }}">
                    Thể loại <span class="material-icons" style="font-size: 16px; vertical-align: middle;">expand_more</span>
                </a>
                <ul class="dropdown-content">
                    {{-- Sách trong nước --}}
                    <li class="dropdown-submenu">
                        <a href="{{ url('/products?loai_sach=trong_nuoc') }}" class="submenu-btn">
                            Sách trong nước <span class="material-icons" style="font-size: 16px; float: right;">chevron_right</span>
                        </a>
                        <ul class="submenu-content">
                            @foreach($menuCategoriesTrongNuoc as $parent)
                                <li class="{{ $parent->children->isNotEmpty() ? 'dropdown-submenu-nested' : '' }}">
                                    <a href="{{ url('/products?loai_sach=trong_nuoc&the_loai_id=' . $parent->id) }}">
                                        {{ $parent->ten_the_loai }}
                                        @if($parent->children->isNotEmpty())
                                            <span class="material-icons" style="font-size: 16px; float: right;">chevron_right</span>
                                        @endif
                                    </a>
                                    @if($parent->children->isNotEmpty())
                                        <ul class="submenu-content-nested">
                                            @foreach($parent->children as $child)
                                                <li>
                                                    <a href="{{ url('/products?loai_sach=trong_nuoc&the_loai_id=' . $child->id) }}">{{ $child->ten_the_loai }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    
                    {{-- Sách nước ngoài --}}
                    <li class="dropdown-submenu">
                        <a href="{{ url('/products?loai_sach=nuoc_ngoai') }}" class="submenu-btn">
                            Sách nước ngoài <span class="material-icons" style="font-size: 16px; float: right;">chevron_right</span>
                        </a>
                        <ul class="submenu-content">
                            @foreach($menuCategoriesNuocNgoai as $parent)
                                <li class="{{ $parent->children->isNotEmpty() ? 'dropdown-submenu-nested' : '' }}">
                                    <a href="{{ url('/products?loai_sach=nuoc_ngoai&the_loai_id=' . $parent->id) }}">
                                        {{ $parent->ten_the_loai }}
                                        @if($parent->children->isNotEmpty())
                                            <span class="material-icons" style="font-size: 16px; float: right;">chevron_right</span>
                                        @endif
                                    </a>
                                    @if($parent->children->isNotEmpty())
                                        <ul class="submenu-content-nested">
                                            @foreach($parent->children as $child)
                                                <li>
                                                    <a href="{{ url('/products?loai_sach=nuoc_ngoai&the_loai_id=' . $child->id) }}">{{ $child->ten_the_loai }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>

            <a href="{{ url('/blog') }}" class="{{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
            <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Liên hệ</a>
        </nav>

        {{-- Search Bar (functional: redirect to /products?search=...) --}}
        <form action="{{ route('products.index') }}" method="GET" class="header-search" id="search-bar" role="search">
            <span class="material-icons search-icon" style="cursor: pointer;" onclick="this.closest('form').submit()">search</span>
            <input
                type="text"
                name="search"
                id="header-search-input"
                placeholder="Tìm sách, tác giả, NXB, NCC..."
                aria-label="Tìm kiếm"
                value="{{ request()->is('products') ? request('search') : '' }}"
                autocomplete="off"
            >
        </form>

        {{-- Actions --}}
        <div class="header-actions">
            <a href="{{ url('/wishlist') }}" class="icon-btn" id="btn-wishlist" title="Danh sách yêu thích">
                <span class="material-icons">favorite_border</span>
            </a>
            <a href="{{ url('/cart') }}" class="icon-btn" id="btn-cart" title="Giỏ hàng">
                <span class="material-icons">shopping_cart</span>
            </a>
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm" id="btn-login">Đăng nhập</a>
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
