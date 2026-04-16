<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHstsMiddleware
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
        $response = $next($request);

        // Bắt buộc trình duyệt phải kết nối qua HTTPS trong 1 năm (Kể cả subdomains)
        // Lưu ý: Chỉ nên bật trên Production thật sự. 
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
