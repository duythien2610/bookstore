<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Đăng nhập') — Modtra Books</title>
    <meta name="description" content="@yield('meta_description', 'Đăng nhập hoặc tạo tài khoản Modtra Books.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/modtra.css') }}">
    @stack('styles')
</head>
<body>
    <div class="auth-wrapper">
        {{-- Branding Panel --}}
        <div class="auth-brand">
            <div class="brand-content">
                <div class="brand-logo">M</div>
                <h1>Modtra Books</h1>
                <p>Khám phá thế giới tri thức cùng hàng ngàn đầu sách chất lượng. Đọc sách mỗi ngày, thay đổi cuộc sống.</p>
            </div>
        </div>

        {{-- Form Panel --}}
        <div class="auth-form-wrapper">
            <div class="auth-form">
                @yield('content')

                <div class="auth-footer">
                    <p>© {{ date('Y') }} Modtra Books. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
