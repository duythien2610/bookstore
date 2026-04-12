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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

    @stack('scripts')
</body>
</html>
