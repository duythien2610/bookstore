<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ghi lại mọi thao tác làm thay đổi dữ liệu của Admin (POST, PUT, DELETE)
        if (Auth::check() && Auth::user()->role_id === 1 && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $logData = [
                'admin_id'   => Auth::id(),
                'email'      => Auth::user()->email,
                'ip_address' => $request->ip(),
                'method'     => $request->method(),
                'url'        => $request->fullUrl(),
                'payload'    => $request->except(['password', 'password_confirmation', '_token']),
            ];

            Log::channel('audit')->info('ADMIN_ACTION', $logData);
        }

        return $next($request);
    }
}
