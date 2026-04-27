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
        /* ═══════════════════════════════════════════════════════════
           TOAST NOTIFICATION
           ─── Fixed to top-right corner as requested
           ═══════════════════════════════════════════════════════════ */
        #toast-container {
            position: fixed;
            top: 80px;
            right: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .v-toast {
            pointer-events: auto;
            min-width: 260px;
            max-width: 380px;
            background-color: #ffffff;
            color: #1f2937;
            padding: 14px 18px;
            border-radius: 10px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, .16), 0 2px 6px rgba(0, 0, 0, .06);
            border-left: 4px solid #16a34a;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.4s ease;
            opacity: 0;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.35;
        }
        .v-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .v-toast.error {
            border-left-color: #dc2626;
        }
        .v-toast-icon {
            font-size: 22px;
            color: #16a34a;
            flex-shrink: 0;
        }
        .v-toast.error .v-toast-icon {
            color: #dc2626;
        }
        @media (max-width: 480px) {
            #toast-container {
                top: 70px; right: 12px; left: 12px;
            }
            .v-toast { min-width: 0; width: 100%; }
        }

        /* ═══════════════════════════════════════════════════════════════
           ADD-TO-CART ANIMATIONS
           ─── Flying image arcs from product card to cart icon,
               cart icon bumps on arrival, and badge pops in scale.
           ═══════════════════════════════════════════════════════════════ */

        /* Cloned image that flies toward cart.
           position is set inline via JS; transforms are handled by WAAPI. */
        .fly-to-cart-img {
            position: fixed;
            z-index: 10000;
            pointer-events: none;
            border-radius: 10px;
            box-shadow: 0 14px 40px rgba(220, 38, 38, 0.35), 0 4px 12px rgba(0,0,0,0.18);
            object-fit: cover;
            will-change: transform, opacity;
            transform-origin: center center;
        }

        /* Soft shockwave ring that emanates from the flying item when it lands. */
        .cart-land-ring {
            position: fixed;
            z-index: 9998;
            pointer-events: none;
            border-radius: 50%;
            border: 2px solid rgba(220, 38, 38, 0.55);
            transform: translate(-50%, -50%) scale(0.2);
            opacity: 0.9;
            will-change: transform, opacity;
        }

        /* Cart icon "bump" — a quick squash + rotate + settle wobble. */
        @keyframes cartBump {
            0%   { transform: translateY(0)      rotate(0)   scale(1); }
            25%  { transform: translateY(-5px)   rotate(-8deg) scale(1.15); }
            45%  { transform: translateY(0)      rotate(6deg)  scale(0.92); }
            65%  { transform: translateY(-2px)   rotate(-3deg) scale(1.05); }
            100% { transform: translateY(0)      rotate(0)   scale(1); }
        }
        #btn-cart.cart-bump .material-icons {
            display: inline-block;
            animation: cartBump 0.65s cubic-bezier(.36,.07,.19,.97) both;
            color: var(--color-primary, #dc2626);
        }

        /* Badge number pop — scale up with bounce, tinted brighter. */
        @keyframes badgePop {
            0%   { transform: scale(1);   box-shadow: 0 0 0 0 rgba(220,38,38,0.6); }
            30%  { transform: scale(1.65); box-shadow: 0 0 0 8px rgba(220,38,38,0); }
            60%  { transform: scale(0.9); }
            100% { transform: scale(1);   box-shadow: 0 0 0 0 rgba(220,38,38,0); }
        }
        #cart-badge.badge-pop {
            animation: badgePop 0.55s cubic-bezier(.36,.07,.19,.97) both;
        }

        /* Small glow pulse on the clicked add-to-cart button while request flies. */
        @keyframes atcBtnPulse {
            0%   { box-shadow: 0 0 0 0   rgba(220,38,38,0.55); }
            100% { box-shadow: 0 0 0 14px rgba(220,38,38,0); }
        }
        .atc-btn-pulse {
            animation: atcBtnPulse 0.6s ease-out both;
        }

        /* Respect users who prefer reduced motion. */
        @media (prefers-reduced-motion: reduce) {
            .fly-to-cart-img,
            .cart-land-ring { display: none !important; }
            #btn-cart.cart-bump .material-icons,
            #cart-badge.badge-pop,
            .atc-btn-pulse { animation: none !important; }
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
            // function hiển thị toast thủ công trên góc trái/phải
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                if (!container) return;
                
                const toast = document.createElement('div');
                toast.className = `v-toast ${type}`;
                const iconName = type === 'success' ? 'check_circle' : 'error';
                
                toast.innerHTML = `<span class="material-icons v-toast-icon">${iconName}</span> <span>${message}</span>`;
                container.appendChild(toast);
                
                setTimeout(() => { toast.classList.add('show'); }, 10);
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 400); 
                }, 3000);
            };

            /* ═══════════════════════════════════════════════════════════
               SESSION FLASH → TOAST
               ─── Fires when a controller returns back()->with('success',...)
                   or ->with('error',...). Covers the non-AJAX add-to-cart
                   path (e.g. CartController@store when JS is disabled or
                   the request is redirected).
               ═══════════════════════════════════════════════════════════ */
            @if (session('success'))
                window.showToast(@json(session('success')), 'success');
            @endif
            @if (session('error'))
                window.showToast(@json(session('error')), 'error');
            @endif

            /* ═══════════════════════════════════════════════════════════
               FLY-TO-CART ANIMATION HELPERS
               ─── Clones the product image, animates it in an arc to the
                   cart icon, then bumps the cart + pops the badge.
               ═══════════════════════════════════════════════════════════ */

            // Resolve the "product image" element to clone for the flight.
            // Falls back gracefully: button → closest .card / .product / form → first <img>.
            function findSourceImage(formEl) {
                if (!formEl) return null;
                const scope =
                    formEl.closest('.card') ||
                    formEl.closest('.product-card') ||
                    formEl.closest('.product-gallery') ||
                    formEl.closest('.product-detail') ||
                    formEl.closest('article') ||
                    formEl.parentElement;
                if (!scope) return null;
                return scope.querySelector('img.card-img, .product-gallery img, img');
            }

            // Animate a cloned <img> from `srcImg` to the cart icon (`#btn-cart`).
            // Uses WAAPI with a 3-keyframe arc so it's smooth on all devices.
            window.flyToCart = function (srcImg) {
                const cartBtn = document.getElementById('btn-cart');
                if (!srcImg || !cartBtn) return Promise.resolve();
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return Promise.resolve();

                const srcRect  = srcImg.getBoundingClientRect();
                const destRect = cartBtn.getBoundingClientRect();
                if (srcRect.width === 0 || destRect.width === 0) return Promise.resolve();

                // Cap the flying image to a reasonable size (some card images are huge).
                const MAX = 140;
                const scaleDown = Math.min(1, MAX / Math.max(srcRect.width, srcRect.height));
                const w = srcRect.width  * scaleDown;
                const h = srcRect.height * scaleDown;

                const clone = document.createElement('img');
                clone.src = srcImg.currentSrc || srcImg.src;
                clone.className = 'fly-to-cart-img';
                clone.style.left   = (srcRect.left + (srcRect.width  - w) / 2) + 'px';
                clone.style.top    = (srcRect.top  + (srcRect.height - h) / 2) + 'px';
                clone.style.width  = w + 'px';
                clone.style.height = h + 'px';
                document.body.appendChild(clone);

                // Compute start → apex → end offsets.  Apex is raised by ~30% of
                // vertical distance for a pleasant arc.
                const startX = 0, startY = 0;
                const endX   = (destRect.left + destRect.width  / 2) - (srcRect.left + srcRect.width  / 2);
                const endY   = (destRect.top  + destRect.height / 2) - (srcRect.top  + srcRect.height / 2);
                const midX   = endX * 0.5;
                const midY   = endY * 0.5 - Math.max(80, Math.abs(endY) * 0.35);

                const anim = clone.animate([
                    { transform: `translate3d(${startX}px, ${startY}px, 0) scale(1)      rotate(0deg)`,   opacity: 1,   offset: 0    },
                    { transform: `translate3d(${midX}px,   ${midY}px,   0) scale(0.65)  rotate(180deg)`, opacity: 0.95, offset: 0.55 },
                    { transform: `translate3d(${endX}px,   ${endY}px,   0) scale(0.15)  rotate(360deg)`, opacity: 0.2,  offset: 1    }
                ], {
                    duration: 850,
                    easing: 'cubic-bezier(.32, .22, .32, 1)',
                    fill: 'forwards'
                });

                return new Promise(resolve => {
                    anim.onfinish = () => {
                        // Shockwave ring at landing point.
                        const ring = document.createElement('div');
                        ring.className = 'cart-land-ring';
                        ring.style.left = (destRect.left + destRect.width  / 2) + 'px';
                        ring.style.top  = (destRect.top  + destRect.height / 2) + 'px';
                        ring.style.width  = '40px';
                        ring.style.height = '40px';
                        document.body.appendChild(ring);
                        ring.animate([
                            { transform: 'translate(-50%, -50%) scale(0.2)', opacity: 0.9 },
                            { transform: 'translate(-50%, -50%) scale(2.4)', opacity: 0   }
                        ], { duration: 600, easing: 'ease-out', fill: 'forwards' })
                        .onfinish = () => ring.remove();

                        clone.remove();
                        resolve();
                    };
                });
            };

            // Play cart bump + badge pop. Restartable (clears the class first).
            window.bumpCart = function () {
                const cartBtn   = document.getElementById('btn-cart');
                const cartBadge = document.getElementById('cart-badge');
                if (cartBtn) {
                    cartBtn.classList.remove('cart-bump');
                    void cartBtn.offsetWidth; // reflow — restart animation
                    cartBtn.classList.add('cart-bump');
                    setTimeout(() => cartBtn.classList.remove('cart-bump'), 700);
                }
                if (cartBadge) {
                    cartBadge.classList.remove('badge-pop');
                    void cartBadge.offsetWidth;
                    cartBadge.classList.add('badge-pop');
                    setTimeout(() => cartBadge.classList.remove('badge-pop'), 600);
                }
            };

            /* ═══════════════════════════════════════════════════════════
               AJAX ADD-TO-CART HANDLER — event delegation so it works
               with cards injected dynamically (pagination, filters...).
               ═══════════════════════════════════════════════════════════ */
            document.addEventListener('submit', function (e) {
                const form = e.target.closest('.ajax-cart-form');
                if (!form) return;
                e.preventDefault();

                const formData = new FormData(form);
                const url = form.getAttribute('action');
                const btn = form.querySelector('button[type="submit"]');
                const srcImg = findSourceImage(form);

                // Kick off the flight IMMEDIATELY (parallel with fetch) for snappy feel.
                if (srcImg) flyToCart(srcImg);
                if (btn) {
                    btn.classList.remove('atc-btn-pulse');
                    void btn.offsetWidth;
                    btn.classList.add('atc-btn-pulse');
                    btn.style.opacity = '0.7';
                    btn.style.pointerEvents = 'none';
                }

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
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
                    if (btn) {
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'auto';
                    }

                    if (data && data.success) {
                        // Sync badge with server count, and schedule the bump so it
                        // plays right as the flying image arrives at the cart icon.
                        const cartBadge = document.getElementById('cart-badge');
                        const applyBump = () => {
                            if (cartBadge) {
                                cartBadge.textContent = data.cart_count;
                                cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
                            }
                            bumpCart();
                        };
                        // 850ms = flight duration. If request finished earlier we
                        // wait for the flight; if it's already done, bump immediately.
                        setTimeout(applyBump, srcImg ? 800 : 0);

                        showToast(data.message, 'success');
                    } else {
                        showToast(data?.message || 'Lỗi thêm giỏ hàng!', 'error');
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi thao tác:', error);
                    if (btn) {
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'auto';
                    }
                    showToast(error.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');

                    if (error.message.includes('Hết phiên làm việc') || error.message.includes('đăng nhập lại')) {
                        setTimeout(() => { window.location.href = '/login'; }, 1500);
                    }
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

    @stack('modals')
    @stack('scripts')
</body>
</html>
