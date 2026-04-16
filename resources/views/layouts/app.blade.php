<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Modtra Books') — Modtra Books</title>
    <meta name="description" content="@yield('meta_description', 'Khám phá thế giới tri thức cùng Modtra Books — Nhà sách trực tuyến hàng đầu.')">
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="@yield('title', 'Modtra Books')">
    <meta property="og:description" content="@yield('meta_description', 'Khám phá thế giới tri thức cùng Modtra Books — Nhà sách trực tuyến hàng đầu.')">
    <meta property="og:image" content="@yield('og_image', asset('images/default-social-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/modtra.css') }}">
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

    @stack('scripts')
</body>
</html>
