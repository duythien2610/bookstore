<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookverse') — Bookverse</title>
    <meta name="description" content="@yield('meta_description', 'Khám phá thế giới tri thức cùng Bookverse — Nhà sách trực tuyến hàng đầu.')">
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="@yield('title', 'Bookverse')">
    <meta property="og:description" content="@yield('meta_description', 'Khám phá thế giới tri thức cùng Bookverse — Nhà sách trực tuyến hàng đầu.')">
    <meta property="og:image" content="@yield('og_image', asset('images/default-social-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bookverse.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        /* CSS cho Toast Notification */
        #toast-container {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
        }
        .v-toast {
            min-width: 250px;
            background-color: var(--color-white);
            color: var(--color-text);
            padding: 15px 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--color-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(-120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.4s ease;
            opacity: 0;
            font-size: var(--font-size-sm);
            font-weight: 500;
        }
        .v-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .v-toast.error {
            border-left-color: var(--color-danger);
        }
        .v-toast-icon {
            font-size: 20px;
            color: var(--color-primary);
        }
        .v-toast.error .v-toast-icon {
            color: var(--color-danger);
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- Ambient autumn maple leaves (position: fixed, z-index: -1, pointer-events: none
         — sits strictly behind all content, cannot intercept clicks / scroll / selection.
         Leaves are injected by JS at end of body. --}}
    <div class="maple-leaves" aria-hidden="true"></div>

    {{-- Subtle grain noise texture overlay (adds warm paper feel) --}}
    <div class="grain-overlay" aria-hidden="true"></div>

    @include('partials._header')

    <main id="main-content">
        @yield('content')
    </main>

    @include('partials._footer')

    @include('partials._chatbot')

    {{-- Toast Container --}}
    <div id="toast-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // function hiển thị toast
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `v-toast ${type}`;
                const iconName = type === 'success' ? 'check_circle' : 'error';
                
                toast.innerHTML = `<span class="material-icons v-toast-icon">${iconName}</span> <span>${message}</span>`;
                container.appendChild(toast);
                
                // Kích hoạt animation
                setTimeout(() => { toast.classList.add('show'); }, 10);
                
                // Tự động ẩn sau 3s
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 400); // Đợi animation trượt ra ngoài
                }, 3000);
            };

            // Lắng nghe tất cả các form ajax-cart-form
            document.querySelectorAll('.ajax-cart-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const url = this.getAttribute('action');
                    
                    // Thêm class loading nếu cần thiết (ví dụ vào nút submit)
                    const btn = this.querySelector('button[type="submit"]');
                    if(btn) {
                        btn.style.opacity = '0.7';
                        btn.style.pointerEvents = 'none';
                    }

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json' // Quan trọng: Báo cho Laravel trả về JSON
                        }
                    })
                    .then(async response => {
                        const data = await response.json().catch(() => null);
                        if (!response.ok) {
                            if (response.status === 419 || response.status === 401) {
                                throw new Error(data?.message || 'Hết phiên làm việc (10 phút). Vui lòng đăng nhập lại!');
                            }
                            throw new Error(data?.message || 'Mạng bị lỗi hoặc sản phẩm không tồn tại.');
                        }
                        return data;
                    })
                    .then(data => {
                        if(btn) {
                            btn.style.opacity = '1';
                            btn.style.pointerEvents = 'auto';
                        }

                        if (data && data.success) {
                            // Cập nhật số lượng giỏ hàng trên header
                            const cartBadge = document.getElementById('cart-badge');
                            if (cartBadge) {
                                cartBadge.textContent = data.cart_count;
                                cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
                                
                                // Hiệu ứng giật nhẹ (pulse)
                                cartBadge.style.transition = 'transform 0.2s';
                                cartBadge.style.transform = 'scale(1.5)';
                                setTimeout(() => { cartBadge.style.transform = 'scale(1)'; }, 200);
                            }
                            // Hiện thông báo
                            showToast(data.message, 'success');
                        } else {
                            showToast(data?.message || 'Lỗi thêm giỏ hàng!', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi thao tác:', error);
                        if(btn) {
                            btn.style.opacity = '1';
                            btn.style.pointerEvents = 'auto';
                        }
                        showToast(error.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');

                        // Nếu là lỗi session, tuỳ chọn chuyển trang (setTimeout 1s ròi reload)
                        if (error.message.includes('Hết phiên làm việc') || error.message.includes('đăng nhập lại')) {
                            setTimeout(() => {
                                window.location.href = '/login';
                            }, 1500);
                        }
                    });
                });
            });
        });
    </script>

    {{-- Header Glass Effect on Scroll --}}
    <script>
    (function() {
        var header = document.querySelector('.site-header');
        if (!header) return;
        var threshold = 40;
        function onScroll() {
            if (window.scrollY > threshold) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    })();
    </script>

    {{-- Scroll Reveal Animation Engine --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.section-header, .features-row .feature-item, .testimonial, .why-card').forEach(function(el) {
            el.classList.add('reveal');
            revealObserver.observe(el);
        });

        document.querySelectorAll('.book-grid .card').forEach(function(el, i) {
            el.style.transitionDelay = (i % 4) * 0.1 + 's';
            el.classList.add('reveal');
            revealObserver.observe(el);
        });

        document.querySelectorAll('.categories-grid .category-card').forEach(function(el, i) {
            el.style.transitionDelay = (i % 4) * 0.08 + 's';
            el.classList.add('reveal');
            revealObserver.observe(el);
        });
    });
    </script>

    {{-- Hero Books Mouse Parallax (applied to container, preserves book hover) --}}
    <script>
    (function() {
        const heroSection = document.querySelector('.hero');
        const heroBooks = document.querySelector('.hero-books');
        if (!heroSection || !heroBooks) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (window.matchMedia('(max-width: 768px)').matches) return;

        let targetX = 0, targetY = 0, currentX = 0, currentY = 0;
        let rafId = null;

        function animate() {
            currentX += (targetX - currentX) * 0.08;
            currentY += (targetY - currentY) * 0.08;
            heroBooks.style.transform = `translate(${currentX}px, ${currentY}px)`;
            if (Math.abs(targetX - currentX) > 0.1 || Math.abs(targetY - currentY) > 0.1) {
                rafId = requestAnimationFrame(animate);
            } else {
                rafId = null;
            }
        }

        heroSection.addEventListener('mousemove', function(e) {
            const rect = heroSection.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            targetX = x * 18;
            targetY = y * 14;
            if (!rafId) animate();
        });

        heroSection.addEventListener('mouseleave', function() {
            targetX = 0;
            targetY = 0;
            if (!rafId) animate();
        });
    })();
    </script>


    {{-- Bookshelf Tab Switcher --}}
    <script>
    (function() {
        const tabs = document.querySelectorAll('.shelf-tab');
        const panels = document.querySelectorAll('.shelf-panel');
        if (!tabs.length) return;

        function activate(targetId) {
            tabs.forEach(t => {
                const on = t.dataset.shelf === targetId;
                t.classList.toggle('active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            panels.forEach(p => {
                p.classList.toggle('active', p.id === targetId);
            });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                activate(this.dataset.shelf);
            });
            tab.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                    e.preventDefault();
                    const list = Array.from(tabs);
                    const i = list.indexOf(this);
                    const next = e.key === 'ArrowRight'
                        ? list[(i + 1) % list.length]
                        : list[(i - 1 + list.length) % list.length];
                    next.focus();
                    activate(next.dataset.shelf);
                }
            });
        });
    })();
    </script>

    {{-- ============================================================
         Autumn Maple Leaves — dynamic generator
         Generates a set of varied SVG maple leaves with randomized
         size, horizontal position, horizontal drift, animation duration,
         delay (negative so leaves start mid-fall), opacity, and color.
         Palette is strictly warm autumn hues — oranges, burnt reds,
         golden yellows, rust — NO pinks or magentas.
         ============================================================ --}}
    <script>
    (function () {
        const container = document.querySelector('.maple-leaves');
        if (!container) return;

        // Respect user's motion preference — don't even create DOM nodes
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        // Skip on admin pages — keep admin UI clean
        if (document.querySelector('.admin-wrapper')) return;

        // Mid-autumn palette — warm tones, a touch deeper than pastel
        // (user asked for slightly bolder leaves) while still avoiding
        // saturated reds and any pink / magenta. Reads as warm paper cut-
        // outs drifting through the cream gutters.
        const COLORS = [
            '#D89055', // warm apricot
            '#C77B3E', // burnt caramel
            '#B86832', // deep terracotta
            '#D49A3B', // mid amber gold
            '#C38528', // rich amber
            '#A66A2E', // toasted bronze
            '#9C5424', // dark rust
            '#B87B4A'  // muted tan
        ];

        // Clean 5-lobe maple silhouette — scales crisp at any size
        const LEAF_PATH = 'M50,5 L55,25 L70,15 L65,35 L85,30 L75,45 L90,55 L70,60 L75,75 L55,70 L50,95 L45,70 L25,75 L30,60 L10,55 L25,45 L15,30 L35,35 L30,15 L45,25 Z';

        const isMobile = window.matchMedia('(max-width: 768px)').matches;
        // Leaves now fall the FULL document height (document-anchored, not
        // viewport-anchored). At any given time they're distributed along
        // the page via staggered negative delays, so the count controls
        // how dense the rain feels at any scroll position.
        const LEAF_COUNT = isMobile ? 18 : 32;

        const rand = (min, max) => min + Math.random() * (max - min);
        const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];

        // Compute how far each leaf has to fall. The container is absolute-
        // positioned over <body>, so its height ≈ document height. We add
        // a small buffer so leaves fully exit below the last pixel of the
        // page before the animation restarts.
        function getFallDistance() {
            const docHeight = Math.max(
                document.documentElement.scrollHeight,
                document.body.scrollHeight,
                window.innerHeight
            );
            return docHeight + 120;
        }

        // Build leaves once with the initial document height. For long
        // pages the fall takes many seconds — plenty of staggered leaves
        // are always mid-fall across the viewport.
        function buildLeaves(fallDistance) {
            container.innerHTML = '';
            const frag = document.createDocumentFragment();

            for (let i = 0; i < LEAF_COUNT; i++) {
                const size = isMobile ? rand(14, 32) : rand(18, 44);
                const x = rand(-5, 105);
                const drift = rand(-180, 180);
                // Per-leaf fall speed in px/sec → natural variation. Slower
                // than real leaves (~80–140 px/s) for a calm, ambient feel.
                const speed = rand(80, 140);
                const duration = fallDistance / speed;
                const delay = -rand(0, duration);
                const opacity = rand(0.35, 0.65);
                const color = pick(COLORS);

                const leaf = document.createElement('div');
                leaf.className = 'maple-leaf';
                leaf.style.setProperty('--size', size.toFixed(1) + 'px');
                leaf.style.setProperty('--x', x.toFixed(2) + '%');
                leaf.style.setProperty('--drift', drift.toFixed(1) + 'px');
                leaf.style.setProperty('--duration', duration.toFixed(2) + 's');
                leaf.style.setProperty('--delay', delay.toFixed(2) + 's');
                leaf.style.setProperty('--peak-opacity', opacity.toFixed(2));
                leaf.style.setProperty('--fall-distance', fallDistance.toFixed(0) + 'px');
                leaf.style.color = color;

                leaf.innerHTML =
                    '<svg class="maple-leaf-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet" aria-hidden="true" focusable="false">' +
                        '<path d="' + LEAF_PATH + '" fill="currentColor"/>' +
                    '</svg>';

                frag.appendChild(leaf);
            }

            container.appendChild(frag);
        }

        buildLeaves(getFallDistance());

        // Recompute fall distance if the document height changes meaningfully
        // (e.g. images load, content injected, viewport resized). Debounced
        // so we don't rebuild on every resize frame.
        let resizeTimer = null;
        let lastFallDistance = getFallDistance();
        function maybeRebuild() {
            const next = getFallDistance();
            // Only rebuild if the page grew/shrunk by more than 200px —
            // minor shifts aren't worth resetting the animation.
            if (Math.abs(next - lastFallDistance) > 200) {
                lastFallDistance = next;
                buildLeaves(next);
            }
        }
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(maybeRebuild, 300);
        });
        window.addEventListener('load', maybeRebuild);

        // Pause leaves when tab is hidden (saves battery + CPU)
        document.addEventListener('visibilitychange', () => {
            container.style.animationPlayState = document.hidden ? 'paused' : 'running';
            container.querySelectorAll('.maple-leaf, .maple-leaf-svg').forEach(el => {
                el.style.animationPlayState = document.hidden ? 'paused' : 'running';
            });
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
