<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AdminMiddleware — Chỉ cho admin truy cập.
 *
 * ┌──────────────────────────────────────────────────────────┐
 * │  Cách hoạt động:                                         │
 * │                                                          │
 * │  Request đến  →  Đã login?                               │
 * │                    ├─ Không  → redirect /login            │
 * │                    └─ Có     → role_id == 1 (admin)?      │
 * │                                 ├─ Không → 403 Forbidden  │
 * │                                 └─ Có   → cho qua ✅     │
 * └──────────────────────────────────────────────────────────┘
 *
 * Dùng khi: Bảo vệ tất cả route trong /admin/*
 * Chỉ user có role_id = 1 (admin) mới vào được.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // role_id = 1 là admin (đã INSERT vào bảng roles)
        if (Auth::user()->role_id !== 1) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
