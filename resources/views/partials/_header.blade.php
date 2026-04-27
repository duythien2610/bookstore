{{-- Site Header / Navigation --}}
@php
    // ── Lấy TẤT CẢ parent categories (không limit) từ DB ──
    // Ưu tiên dùng biến do View Composer (AppServiceProvider) share sẵn,
    // fallback query trực tiếp nếu biến chưa có.
    if (!isset($menuCategoriesTrongNuoc) || !$menuCategoriesTrongNuoc->count()) {
        $menuCategoriesTrongNuoc = \App\Models\TheLoai::with(['children' => function($q){ $q->orderBy('ten_the_loai'); }])
            ->whereNull('parent_id')
            ->whereIn('loai_sach', ['trong_nuoc', 'tat_ca'])
            ->orderBy('ten_the_loai')
            ->get();
    }
    if (!isset($menuCategoriesNuocNgoai) || !$menuCategoriesNuocNgoai->count()) {
        $menuCategoriesNuocNgoai = \App\Models\TheLoai::with(['children' => function($q){ $q->orderBy('ten_the_loai'); }])
            ->whereNull('parent_id')
            ->whereIn('loai_sach', ['nuoc_ngoai', 'tat_ca'])
            ->orderBy('ten_the_loai')
            ->get();
    }

    // Gộp thành danh sách phẳng: [ ['section_header' => 'Sách Trong Nước'], parent1, parent2, ..., ['section_header' => 'Foreign Books'], parent3, ... ]
    $megaSidebarItems = collect();
    if ($menuCategoriesTrongNuoc->count()) {
        $megaSidebarItems->push(['type' => 'header', 'label' => 'SÁCH TRONG NƯỚC']);
        foreach ($menuCategoriesTrongNuoc as $p) {
            $megaSidebarItems->push(['type' => 'parent', 'parent' => $p]);
        }
    }
    if ($menuCategoriesNuocNgoai->count()) {
        $megaSidebarItems->push(['type' => 'header', 'label' => 'FOREIGN BOOKS']);
        foreach ($menuCategoriesNuocNgoai as $p) {
            $megaSidebarItems->push(['type' => 'parent', 'parent' => $p]);
        }
    }

    // Pill promo dưới cùng mỗi panel
    $megaPromos = [
        ['label' => 'SÁCH MỚI',  'url' => url('/products?sort=newest')],
        ['label' => 'NỔI BẬT',   'url' => route('products.featured')],
        ['label' => 'BÁN CHẠY',  'url' => route('products.bestselling')],
    ];
@endphp

<header class="site-header">
    <div class="container">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="site-logo" id="site-logo">
            <img src="{{ asset('images/bookverse-logo.png') }}" alt="Bookverse" class="site-logo__img">
        </a>

        {{-- Navigation --}}
        <nav class="nav-links" id="main-nav">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
            <a href="{{ url('/products') }}" class="{{ (request()->is('products') && !request()->has('view')) ? 'active' : '' }}">Sách mới</a>

            {{-- ── Mega Menu: Thể loại ── --}}
            <div class="nav-mega">
                <a href="{{ url('/products?view=categories') }}"
                   class="nav-mega__trigger {{ request('view') == 'categories' ? 'active' : '' }}">
                    Thể loại
                    <span class="material-icons nav-mega__caret">expand_more</span>
                </a>

                <div class="mega-menu" role="menu" aria-label="Danh sách thể loại">
                    {{-- LEFT: sidebar with ALL parent categories ── --}}
                    <aside class="mega-sidebar">
                        <h3 class="mega-sidebar__title">Danh mục sản phẩm</h3>
                        <ul class="mega-sidebar__list">
                            @php $parentIdx = 0; @endphp
                            @foreach($megaSidebarItems as $item)
                                @if($item['type'] === 'header')
                                    <li class="mega-section-header">{{ $item['label'] }}</li>
                                @else
                                    @php $parent = $item['parent']; @endphp
                                    <li class="mega-group {{ $parentIdx === 0 ? 'is-default' : '' }}">
                                        <a href="{{ url('/products?' . http_build_query(['category' => [$parent->ten_the_loai]])) }}" class="mega-group__link">
                                            <span class="material-icons mega-group__icon">book</span>
                                            <span class="mega-group__label">{{ $parent->ten_the_loai }}</span>
                                            <span class="material-icons mega-group__arrow">chevron_right</span>
                                        </a>

                                        {{-- RIGHT: content panel (children of this parent) --}}
                                        <div class="mega-panel" role="menu">
                                            <div class="mega-panel__head">
                                                <h2 class="mega-panel__title">
                                                    <span class="material-icons">book</span>
                                                    {{ $parent->ten_the_loai }}
                                                </h2>
                                                <a href="{{ url('/products?' . http_build_query(['category' => [$parent->ten_the_loai]])) }}" class="mega-panel__viewall-top">
                                                    Xem tất cả
                                                    <span class="material-icons">arrow_forward</span>
                                                </a>
                                            </div>

                                            @if($parent->children && $parent->children->count())
                                                <ul class="mega-panel__children">
                                                    @foreach($parent->children as $child)
                                                        <li>
                                                            <a href="{{ url('/products?' . http_build_query(['category' => [$child->ten_the_loai]])) }}">
                                                                {{ $child->ten_the_loai }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="mega-panel__empty">Chưa có thể loại con.</p>
                                            @endif

                                            {{-- Promo pills --}}
                                            <div class="mega-panel__promos">
                                                @foreach($megaPromos as $promo)
                                                    <a href="{{ $promo['url'] }}" class="mega-promo-pill">
                                                        {{ $promo['label'] }}
                                                        <span class="mega-promo-pill__heart">♥</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </li>
                                    @php $parentIdx++; @endphp
                                @endif
                            @endforeach
                        </ul>
                    </aside>
                </div>
            </div>

            <a href="{{ route('products.featured') }}" class="{{ request()->is('featured-books') ? 'active' : '' }}">Nổi bật</a>
            <a href="{{ route('products.bestselling') }}" class="{{ request()->is('best-selling') ? 'active' : '' }}">Bán chạy</a>
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
                                                <div class="result-price">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(book.gia_ban)}</div>
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
                <span class="material-icons">shopping_bag</span>
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
                @if(Auth::user()->isAdmin())
                    <a href="{{ url('/admin') }}" class="icon-btn" title="Quản trị hệ thống" style="color: var(--color-primary-dark);">
                        <span class="material-icons">admin_panel_settings</span>
                    </a>
                @endif
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

            <button class="mobile-menu-toggle" id="mobile-menu-toggle" title="Menu">
                <span class="material-icons">menu</span>
            </button>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('mobile-menu-toggle');
    const nav = document.getElementById('main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function() {
            nav.classList.toggle('active');
            const icon = this.querySelector('.material-icons');
            icon.textContent = nav.classList.contains('active') ? 'close' : 'menu';
        });
    }
});
</script>

{{-- ─────────────────────────────────────────
     Mega Menu styles (pure CSS, no JS needed)
     ───────────────────────────────────────── --}}
<style>
/* ═══════════════════════════════════════════════════════════════
   MEGA MENU — Bookverse
   Pure CSS, no JS. Triggered by :hover on .nav-mega
   ═══════════════════════════════════════════════════════════════ */

/* ── Ensure header always sits above hero/page content ── */
.site-header {
    position: relative;
    z-index: 9000;
}

/* ── Trigger on nav ──
   NOTE: .nav-mega must be position: static so that the absolute-positioned
   .mega-menu inside it anchors to .site-header (which is position: relative)
   instead of the narrow trigger. That way the menu spans full header width
   and can be centered on the viewport rather than on the "Thể loại" text.
   align-self: stretch makes the hover zone cover the FULL vertical height of
   the header — so moving the mouse downward from the text never leaves hover. */
.nav-mega {
    position: static;
    display: inline-flex;
    align-items: center;
    align-self: stretch;
}
.nav-mega__trigger {
    display: inline-flex !important;
    align-items: center;
    gap: 2px;
}
.nav-mega__caret {
    font-size: 18px !important;
    transition: transform .25s ease;
}
.nav-mega:hover .nav-mega__caret,
.nav-mega.is-open .nav-mega__caret { transform: rotate(180deg); }

/* ── Panel container ──
   Anchored to .site-header (full-width), centered on viewport.
   No max-height / no scrollbar — expands downward naturally with content. */
.mega-menu {
    position: absolute;
    top: 100%;           /* flush against header bottom — NO margin-top gap */
    left: 50%;
    transform: translateX(-50%) translateY(8px);
    width: min(1100px, 96vw);
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 20px 50px rgba(17, 24, 39, .15), 0 4px 10px rgba(17,24,39,.06);
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .22s ease, transform .22s ease, visibility 0s linear .22s;
    z-index: 9999;
}
/* Invisible bridge that spans the entire header height above the menu.
   Ensures no dead-zone between the trigger text and the dropdown panel. */
.mega-menu::before {
    content: "";
    position: absolute;
    top: -80px; left: 0; right: 0; height: 80px;
    background: transparent;
    pointer-events: auto;
}
.nav-mega:hover .mega-menu,
.nav-mega:focus-within .mega-menu,
.nav-mega.is-open .mega-menu {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translateX(-50%) translateY(0);
    transition: opacity .22s ease, transform .22s ease, visibility 0s;
    animation: megaFadeIn .25s ease both;
}
@keyframes megaFadeIn {
    from { opacity: 0; transform: translateX(-50%) translateY(10px); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}

/* ─────────────── LEFT SIDEBAR ───────────────
   No max-height, no scrollbar — shows ALL categories.
   IMPORTANT: position must be STATIC so that absolute-positioned .mega-panel
   children anchor to .mega-menu (1100px wide), not to this 280px sidebar. */
.mega-sidebar {
    width: 280px;
    align-self: stretch;
    background: #fff;
    border-right: 1px solid #f0f0f2;
    padding: 20px 0 16px;
    flex-shrink: 0;
    position: static;
    overflow: visible;
    max-height: none;
}
.mega-sidebar__list {
    max-height: none;
    overflow: visible;
}

/* Section header in sidebar (non-clickable, visual separator) */
.mega-section-header {
    list-style: none;
    padding: 14px 22px 6px;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .8px;
    color: #9ca3af;
    text-transform: uppercase;
    border-top: 1px solid #f3f4f6;
    pointer-events: none;
}
.mega-section-header:first-child {
    border-top: none;
    padding-top: 4px;
}
.mega-sidebar__title {
    margin: 0 0 14px 0;
    padding: 0 22px;
    font-size: .95rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: .2px;
}
.mega-sidebar__list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.mega-group {
    position: static; /* so its absolute panel anchors to .mega-menu */
    list-style: none;
}
.mega-group__link {
    display: flex !important;
    align-items: center;
    gap: 10px;
    padding: 10px 22px;
    font-size: .85rem;
    font-weight: 600;
    color: #374151 !important;
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: background .15s, color .15s, border-color .15s;
}
.mega-group__icon {
    font-size: 20px !important;
    color: #9ca3af;
    flex-shrink: 0;
    transition: color .15s;
}
.mega-group__label {
    flex: 1;
    text-transform: uppercase;
    letter-spacing: .3px;
    font-size: .82rem;
}
.mega-group__arrow {
    font-size: 18px !important;
    color: #d1d5db;
    opacity: 0;
    transform: translateX(-4px);
    transition: opacity .15s, transform .15s, color .15s;
}
.mega-group:hover > .mega-group__link {
    background: #fef2f2;
    color: #dc2626 !important;
    border-left-color: #dc2626;
}
.mega-group:hover > .mega-group__link .mega-group__icon,
.mega-group:hover > .mega-group__link .mega-group__arrow {
    color: #dc2626;
}
.mega-group:hover > .mega-group__link .mega-group__arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Wrap whole menu as flex so sidebar + panel stay side by side */
.mega-menu { display: flex; }

/* ─────────────── RIGHT PANEL ───────────────
   Anchored to .mega-menu (1100px wide) because every ancestor up to it is
   position: static. left:280px + right:0 => panel width = mega-menu - 280. */
.mega-panel {
    position: absolute;
    top: 0;
    left: 280px;   /* match sidebar width */
    right: 0;
    bottom: 0;
    padding: 22px 30px 20px;
    background: #fff;
    display: none;
    flex-direction: column;
    overflow: visible;
    min-width: 0;
    box-sizing: border-box;
}
.mega-group:hover > .mega-panel {
    display: flex;
    animation: panelFade .2s ease both;
}
/* Default panel (first group) when not hovering any sidebar item */
.mega-sidebar__list:not(:hover) .mega-group.is-default > .mega-panel {
    display: flex;
    animation: panelFade .2s ease both;
}
@keyframes panelFade {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Panel header */
.mega-panel__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-bottom: 14px;
    margin-bottom: 18px;
    border-bottom: 1px solid #f0f0f2;
}
.mega-panel__title {
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: .2px;
}
.mega-panel__title .material-icons {
    font-size: 24px;
    color: #dc2626;
    padding: 6px;
    background: #fef2f2;
    border-radius: 8px;
}
.mega-panel__viewall-top {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .82rem;
    font-weight: 700;
    color: #dc2626;
    text-decoration: none;
    white-space: nowrap;
    transition: color .15s;
}
.mega-panel__viewall-top:hover { color: #b91c1c; text-decoration: underline; }
.mega-panel__viewall-top .material-icons { font-size: 16px; }

.mega-panel__empty {
    margin: 0 auto;
    padding: 2rem 0;
    font-size: .9rem;
    color: #9ca3af;
    font-style: italic;
    text-align: center;
}

/* Children list — grid of child category links */
.mega-panel__children {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 4px 20px;
    flex: 1 1 auto;
    align-content: start;
    width: 100%;
    min-width: 0;
}
.mega-panel__children li { margin: 0; min-width: 0; }
.mega-panel__children li a {
    display: block;
    padding: 7px 10px;
    font-size: .88rem;
    color: #4b5563;
    text-decoration: none;
    border-radius: 6px;
    transition: background .15s, color .15s, padding-left .15s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mega-panel__children li a:hover {
    background: #fef2f2;
    color: #dc2626;
    padding-left: 14px;
}

/* Promo pills at bottom */
.mega-panel__promos {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 18px;
    margin-top: 18px;
    border-top: 1px solid #f0f0f2;
}
.mega-promo-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    font-size: .78rem;
    font-weight: 800;
    letter-spacing: .4px;
    color: #dc2626;
    background: #fff;
    border: 1.5px solid #dc2626;
    border-radius: 999px;
    text-decoration: none;
    transition: background .15s, color .15s, transform .1s;
}
.mega-promo-pill:hover {
    background: #dc2626;
    color: #fff;
}
.mega-promo-pill:active { transform: scale(.97); }
.mega-promo-pill__heart {
    font-size: .9rem;
    line-height: 1;
}

/* ── Responsive: hide mega on mobile, fallback to normal link ── */
@media (max-width: 900px) {
    .nav-mega__caret { display: none; }
    .mega-menu { display: none !important; }
}
</style>

{{-- ── JS: keep mega menu open with a small close delay so the user
        can cross the gap between trigger and panel without it closing. ── --}}
<script>
(function () {
    const navMega = document.querySelector('.nav-mega');
    if (!navMega) return;

    let closeTimer = null;
    const CLOSE_DELAY = 200;

    const open = () => {
        clearTimeout(closeTimer);
        navMega.classList.add('is-open');
    };
    const scheduleClose = () => {
        clearTimeout(closeTimer);
        closeTimer = setTimeout(() => navMega.classList.remove('is-open'), CLOSE_DELAY);
    };

    navMega.addEventListener('mouseenter', open);
    navMega.addEventListener('mouseleave', scheduleClose);
    navMega.addEventListener('focusin', open);
    navMega.addEventListener('focusout', (e) => {
        if (!navMega.contains(e.relatedTarget)) scheduleClose();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') navMega.classList.remove('is-open');
    });
})();
</script>
