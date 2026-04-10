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
            <a href="{{ url('/products') }}" class="{{ (request()->is('products') && !request()->has('view')) ? 'active' : '' }}">Sách mới</a>
            <a href="{{ url('/products?view=categories') }}" class="{{ request('view') == 'categories' ? 'active' : '' }}">Thể loại</a>
            <a href="{{ route('tracking.search') }}" class="{{ request()->is('track-order*') ? 'active' : '' }}">Theo dõi đơn</a>
            <a href="{{ url('/blog') }}" class="{{ request()->is('blog*') ? 'active' : '' }}">Blog</a>
            <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Liên hệ</a>
        </nav>

        {{-- Search --}}
        <form action="{{ route('products.index') }}" method="GET" class="header-search" id="search-bar">
            <span class="material-icons search-icon">search</span>
            <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sách, tác giả..." aria-label="Tìm kiếm" autocomplete="off">
            <div id="search-results" class="search-results-dropdown"></div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search-input');
                const searchResults = document.getElementById('search-results');
                let timeout = null;

                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    const query = this.value.trim();

                    if (query.length < 1) {
                        searchResults.innerHTML = '';
                        searchResults.style.display = 'none';
                        return;
                    }

                    timeout = setTimeout(() => {
                        fetch(`{{ route('api.search') }}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                searchResults.innerHTML = '';
                                if (data.length > 0) {
                                    data.forEach(book => {
                                        const imageUrl = book.link_anh_bia || (book.file_anh_bia ? `/uploads/books/${book.file_anh_bia}` : 'https://placehold.co/150x200?text=No+Image');
                                        const resultItem = document.createElement('a');
                                        resultItem.href = `/products/${book.id}`;
                                        resultItem.className = 'search-result-item';
                                        resultItem.innerHTML = `
                                            <img src="${imageUrl}" alt="${book.tieu_de}" class="result-img">
                                            <div class="result-info">
                                                <div class="result-title">${book.tieu_de}</div>
                                                <div class="result-author" style="font-size: 11px; color: var(--color-text-secondary); margin-bottom: 2px;">${book.ten_tac_gia}</div>
                                                <div class="result-price" style="font-size: 12px; color: var(--color-primary-dark); font-weight: bold;">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(book.gia_ban)}</div>
                                            </div>
                                        `;
                                        searchResults.appendChild(resultItem);
                                    });
                                } else {
                                    searchResults.innerHTML = '<div class="no-results">Không có kết quả tìm kiếm phù hợp</div>';
                                }
                                searchResults.style.display = 'block';
                            })
                            .catch(error => console.error('Error fetching search results:', error));
                    }, 300);
                });

                // Close results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!document.getElementById('search-bar').contains(e.target)) {
                        searchResults.style.display = 'none';
                    }
                });
            });
        </script>

        {{-- Actions --}}
        <div class="header-actions">
            <a href="{{ url('/wishlist') }}" class="icon-btn" id="btn-wishlist" title="Danh sách yêu thích">
                <span class="material-icons">favorite_border</span>
            </a>
            <a href="{{ url('/cart') }}" class="icon-btn" id="btn-cart" title="Giỏ hàng">
                <span class="material-icons">shopping_cart</span>
                @php
                    $cartCount = 0;
                    if (Auth::check()) {
                        $gioHang = \App\Models\GioHang::where('user_id', Auth::id())->where('trang_thai', 'active')->first();
                        $cartCount = $gioHang ? $gioHang->chiTiets()->sum('so_luong') : 0;
                    } else {
                        $cartCount = session('cart') ? array_sum(array_column(session('cart'), 'so_luong')) : 0;
                    }
                @endphp
                <span class="badge" id="cart-badge" style="{{ $cartCount > 0 ? '' : 'display: none;' }}">{{ $cartCount }}</span>
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
