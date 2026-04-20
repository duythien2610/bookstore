<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Bookverse Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bookverse.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        @include('partials._admin-sidebar')

        <div class="admin-main">
            @include('partials._admin-topbar')

            <div class="admin-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle (mobile) + user menu dropdown
        (function () {
            const body    = document.body;
            const toggle  = document.getElementById('admin-sidebar-toggle');
            const sidebar = document.getElementById('admin-sidebar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', () => body.classList.toggle('admin-sidebar-open'));
                document.addEventListener('click', (e) => {
                    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                        body.classList.remove('admin-sidebar-open');
                    }
                });
            }

            const userMenu = document.querySelector('.admin-navbar__user');
            if (userMenu) {
                userMenu.addEventListener('click', (e) => {
                    if (e.target.closest('.admin-navbar__menu')) return;
                    userMenu.classList.toggle('is-open');
                });
                document.addEventListener('click', (e) => {
                    if (!userMenu.contains(e.target)) userMenu.classList.remove('is-open');
                });
            }

            // Ctrl+K shortcut → focus search
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    const input = document.querySelector('.admin-navbar__search input');
                    if (input) { e.preventDefault(); input.focus(); }
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
