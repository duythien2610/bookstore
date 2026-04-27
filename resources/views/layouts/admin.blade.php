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

    <style>
        /* ═══════════════════════════════════════════════════════════════
           ADMIN LOCAL SEARCH — shared styles for Books / Orders / Coupons
           search inputs.  Provides spinner, clear button, and loading
           overlay with a centered spinner over the results table.
           ═══════════════════════════════════════════════════════════════ */
        .header-search { position: relative; }
        .header-search input { padding-right: 58px; }

        .js-admin-search-spinner {
            position: absolute;
            right: 36px;
            top: 50%;
            width: 14px;
            height: 14px;
            margin-top: -7px;
            border-radius: 50%;
            border: 2px solid var(--color-border-light, #e5e7eb);
            border-top-color: var(--color-primary, #4f46e5);
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s ease;
        }
        .header-search.is-loading .js-admin-search-spinner {
            opacity: 1;
            animation: adminSearchSpin .7s linear infinite;
        }
        @keyframes adminSearchSpin {
            to { transform: rotate(360deg); }
        }

        .js-admin-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            color: var(--color-text-muted, #6b7280);
            border-radius: 50%;
            transition: background .15s ease, color .15s ease;
        }
        .js-admin-search-clear:hover {
            background: var(--color-bg-alt, #f3f4f6);
            color: var(--color-danger, #dc2626);
        }
        .js-admin-search-clear .material-icons { font-size: 16px; }
        .header-search.has-value .js-admin-search-clear { display: inline-flex; }

        /* Loading overlay inside the table-wrapper. */
        .js-admin-search-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(1px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 5;
            border-radius: inherit;
        }
        .js-admin-search-target.is-loading .js-admin-search-overlay {
            display: flex;
        }
        .js-admin-search-overlay__spinner {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 3px solid var(--color-border-light, #e5e7eb);
            border-top-color: var(--color-primary, #4f46e5);
            animation: adminSearchSpin .8s linear infinite;
        }
        .js-admin-search-target.is-loading .js-admin-search-rows {
            opacity: 0.5;
            transition: opacity .15s ease;
        }
    </style>

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

        })();
    </script>

    {{-- ═══════════════════════════════════════════════════════════════
         ADMIN LOCAL SEARCH — shared AJAX search behavior.
         Any page can opt-in by rendering:
           <form class="js-admin-search-form" action="{{ ENDPOINT }}">
             <input name="search" ...>
             <span class="js-admin-search-spinner"></span>
             <button class="js-admin-search-clear"></button>
           </form>
           <div class="js-admin-search-target" data-endpoint="{{ ENDPOINT }}">
             <tbody class="js-admin-search-rows">...</tbody>
             <div class="js-admin-search-overlay"></div>
           </div>

         Backend must return JSON: { success, count, html } on XHR requests.
         Features: debounced real-time filtering, Enter-key submit, loading
         state (spinner + dim rows), 'No results' (rendered by backend
         partial via @forelse...@empty), and auto-reset on cleared input.
         ═══════════════════════════════════════════════════════════════ --}}
    <script>
    (function () {
        const DEBOUNCE_MS = 350;

        document.querySelectorAll('.js-admin-search-form').forEach(function (form) {
            const input  = form.querySelector('input[name="search"]');
            if (!input) return;

            // Find the closest target container (table-wrapper) to update.
            // If more than one exists per page, we use the next target in the DOM.
            const target = document.querySelector('.js-admin-search-target');
            if (!target) return;

            const rowsHost = target.querySelector('.js-admin-search-rows');
            const clearBtn = form.querySelector('.js-admin-search-clear');
            const endpoint = target.dataset.endpoint || form.getAttribute('action');

            let debounceTimer = null;
            let activeController = null;

            const syncClearVisibility = function () {
                form.classList.toggle('has-value', input.value.trim() !== '');
            };
            syncClearVisibility();

            const buildQuery = function () {
                // Include all form fields (search + any extra filters like date)
                // PLUS any existing URL params so filters from page state persist.
                const params = new URLSearchParams();
                new FormData(form).forEach(function (v, k) {
                    if (v !== null && v !== undefined && String(v).length) {
                        params.append(k, v);
                    }
                });
                return params.toString();
            };

            const run = function () {
                if (activeController) activeController.abort();
                activeController = new AbortController();

                target.classList.add('is-loading');
                form.classList.add('is-loading');

                const qs  = buildQuery();
                const url = endpoint + (endpoint.includes('?') ? '&' : '?') + qs;

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    signal: activeController.signal
                })
                .then(function (res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    if (rowsHost && typeof data.html === 'string') {
                        rowsHost.innerHTML = data.html;
                    }

                    // Update URL (so refresh preserves search) without navigation.
                    try {
                        const next = new URL(window.location.href);
                        if (input.value.trim()) next.searchParams.set('search', input.value.trim());
                        else next.searchParams.delete('search');
                        window.history.replaceState({}, '', next.toString());
                    } catch (_) { /* ignore */ }
                })
                .catch(function (err) {
                    if (err && err.name === 'AbortError') return;
                    console.error('[admin-search]', err);
                    if (rowsHost) {
                        rowsHost.innerHTML =
                            '<tr><td colspan="99" style="text-align:center; padding:2rem; color:var(--color-danger);">' +
                            'Lỗi tải dữ liệu. Vui lòng thử lại.' +
                            '</td></tr>';
                    }
                })
                .finally(function () {
                    target.classList.remove('is-loading');
                    form.classList.remove('is-loading');
                });
            };

            // Real-time: debounced fetch on every keystroke.
            input.addEventListener('input', function () {
                syncClearVisibility();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(run, DEBOUNCE_MS);
            });

            // Enter key → submit immediately (bypass debounce). Prevent page reload.
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                run();
            });

            // Extra filter fields (e.g. date picker on Orders) should trigger too.
            form.querySelectorAll('.js-admin-search-extra').forEach(function (el) {
                el.addEventListener('change', function () {
                    clearTimeout(debounceTimer);
                    run();
                });
            });

            // Clear button → empty input, auto-reset list to show all data.
            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    input.value = '';
                    syncClearVisibility();
                    input.focus();
                    clearTimeout(debounceTimer);
                    run();
                });
            }

            // Esc while focused → clear (matches common UX).
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && input.value) {
                    input.value = '';
                    syncClearVisibility();
                    clearTimeout(debounceTimer);
                    run();
                }
            });
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
