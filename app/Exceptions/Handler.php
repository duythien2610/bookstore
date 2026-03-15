<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Hết phiên làm việc (10 phút). Vui lòng tải lại trang và đăng nhập lại!'], 419);
            }
            return redirect()->route('login')->with('error', 'Hết phiên làm việc (10 phút). Vui lòng tải lại trang và đăng nhập lại!');
        });

        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Hết phiên làm việc (10 phút). Vui lòng đăng nhập lại!'], 401);
            }
            return redirect()->route('login')->with('error', 'Hết phiên làm việc (10 phút). Vui lòng đăng nhập lại!');
        });
    }
}
