<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * EnsureEmailIsVerified — Middleware chặn user chưa xác thực email.
 *
 * ┌──────────────────────────────────────────────────────────┐
 * │  Cách hoạt động:                                         │
 * │                                                          │
 * │  Request đến  →  Đã login?                               │
 * │                    ├─ Không  → redirect /login            │
 * │                    └─ Có     → email_verified_at != null? │
 * │                                 ├─ Không → redirect       │
 * │                                 │         /verify-email   │
 * │                                 └─ Có   → cho qua ✅     │
 * └──────────────────────────────────────────────────────────┘
 *
 * Dùng khi: Bạn muốn một route CHỈ cho phép user đã xác thực
 * email truy cập (ví dụ: đặt hàng, thanh toán, viết đánh giá...)
 */
class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Chưa login → redirect login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Đã login nhưng chưa verify email → redirect verify
        if (is_null(Auth::user()->email_verified_at)) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Bạn cần xác thực email trước khi thực hiện thao tác này.');
        }

        return $next($request);
    }
}
