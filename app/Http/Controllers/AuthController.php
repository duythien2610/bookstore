<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Mail\PasswordResetCodeMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // =====================================================================
    //  ĐĂNG NHẬP
    // =====================================================================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        // BẢO VỆ BRUTE FORCE TRÊN ACCOUNT (Quá 5 lần sai -> Khóa 15 phút)
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'email' => "Tài khoản của bạn đã bị khóa tạm thời do nhập sai quá nhiều lần. Vui lòng thử lại sau {$minutes} phút."
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();

            // Đăng xuất và vô hiệu hóa tất cả các phiên đăng nhập khác của tài khoản này
            Auth::logoutOtherDevices($request->input('password'));

            $user = Auth::user();

            // Load Cart from DB into session
            $gioHang = \App\Models\GioHang::with('chiTiets.sach.tacGia')->where('user_id', $user->id)->first();
            if ($gioHang && $gioHang->chiTiets->count() > 0) {
                $cart = [];
                foreach ($gioHang->chiTiets as $ct) {
                    if ($ct->sach) {
                        $cart[(string)$ct->sach_id] = [
                            'sach_id'    => $ct->sach_id,
                            'tieu_de'    => $ct->sach->tieu_de,
                            'ten_tac_gia'=> $ct->sach->tacGia->ten_tac_gia ?? 'Không rõ',
                            'gia_ban'    => (int) $ct->don_gia,
                            'gia_goc'    => (int) ($ct->sach->gia_goc ?? 0),
                            'anh_bia'    => $ct->sach->file_anh_bia ?? $ct->sach->link_anh_bia,
                            'so_luong'   => (int) $ct->so_luong,
                            'ton_kho'    => (int) $ct->sach->so_luong_ton,
                        ];
                    }
                }
                session(['cart' => $cart]);
            }

            // Load Wishlist from DB into session
            $wishlists = \App\Models\Wishlist::where('user_id', $user->id)->pluck('sach_id');
            if ($wishlists->count() > 0) {
                $list = [];
                foreach ($wishlists as $sId) {
                    $list[(string)$sId] = true;
                }
                session(['wishlist' => $list]);
            }

            if (is_null($user->email_verified_at)) {
                return redirect()->route('verification.notice');
            }

            return redirect()->intended('/');
        }

        // Đếm số lần sai, khóa 15 phút (900 giây) nếu quá 5 lần
        RateLimiter::hit($throttleKey, 900);
        $attemptsLeft = RateLimiter::retriesLeft($throttleKey, 5);

        return back()->withErrors([
            'email' => "Email hoặc mật khẩu không chính xác. Bạn còn {$attemptsLeft} lần thử trước khi bị khóa tạm thời.",
        ])->onlyInput('email');
    }

    // =====================================================================
    //  ĐĂNG KÝ
    // =====================================================================

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'Vui lòng nhập họ và tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'email.unique'       => 'Email này đã được sử dụng.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $userRole = Role::where('ten_vai_tro', 'user')->first();

        $user = User::create([
            'ho_ten'        => $validated['name'],
            'email'         => $validated['email'],
            'so_dien_thoai' => $validated['phone'] ?? null,
            'password'      => Hash::make($validated['password']),
            'role_id'       => $userRole ? $userRole->id : 2,
        ]);

        Auth::login($user);
        $this->sendVerificationCode($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
    }

    // =====================================================================
    //  ĐĂNG XUẤT
    // =====================================================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Đổi mật khẩu cho user đang đăng nhập.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'old_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'password.min'          => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed'    => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        /** @var \App\Models\User $user */
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Đã cập nhật mật khẩu thành công!');
    }

    // =====================================================================
    //  XÁC THỰC EMAIL — SECURITY 3 LỚP
    // =====================================================================
    //  Lớp 1: Session-bound → middleware('auth')
    //  Lớp 2: OTP hash → Hash::make()
    //  Lớp 3: Expiry (10 phút) + Attempt limit (5 lần)

    public function showVerifyEmail()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect('/');
        }

        return view('auth.verify-email', ['email' => $user->email]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'otp'   => ['required', 'array', 'size:6'],
            'otp.*' => ['required', 'string', 'size:1'],
        ], [
            'otp.required' => 'Vui lòng nhập mã xác thực.',
        ]);

        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect('/');
        }

        $inputCode = implode('', $request->otp);

        $verification = DB::table('email_verifications')
            ->where('user_id', $user->id)
            ->first();

        if (!$verification) {
            return back()->withErrors(['otp' => 'Bạn chưa có mã xác thực. Vui lòng nhấn "Gửi lại mã".']);
        }

        if (Carbon::parse($verification->expires_at)->isPast()) {
            DB::table('email_verifications')->where('user_id', $user->id)->delete();
            return back()->withErrors(['otp' => 'Mã đã hết hạn. Vui lòng nhấn "Gửi lại mã".']);
        }

        if ($verification->attempts >= 5) {
            DB::table('email_verifications')->where('user_id', $user->id)->delete();
            return back()->withErrors(['otp' => 'Nhập sai quá 5 lần. Mã đã bị hủy. Vui lòng nhấn "Gửi lại mã".']);
        }

        if (!Hash::check($inputCode, $verification->code_hash)) {
            $attemptsLeft = 4 - $verification->attempts;
            DB::table('email_verifications')->where('user_id', $user->id)->increment('attempts');
            return back()->withErrors(['otp' => "Mã không đúng. Bạn còn {$attemptsLeft} lần thử."]);
        }

        // ✅ Xác thực thành công
        /** @var \App\Models\User $user */
        $user->email_verified_at = now();
        $user->save();
        DB::table('email_verifications')->where('user_id', $user->id)->delete();

        return redirect()->route('verification.success')
            ->with('success', 'Email đã được xác thực thành công!');
    }

    public function resendCode(Request $request)
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect('/');
        }

        $lastSent = DB::table('email_verifications')
            ->where('user_id', $user->id)
            ->value('created_at');

        if ($lastSent && Carbon::parse($lastSent)->diffInSeconds(now()) < 60) {
            $waitSeconds = 60 - Carbon::parse($lastSent)->diffInSeconds(now());
            return back()->with('resend_error', "Vui lòng đợi {$waitSeconds} giây trước khi gửi lại.");
        }

        $this->sendVerificationCode($user);

        return back()->with('resend_success', 'Mã xác thực mới đã được gửi đến email của bạn!');
    }

    // =====================================================================
    //  QUÊN MẬT KHẨU — SECURITY 3 LỚP
    // =====================================================================
    //  Lớp 1: Session-bound email → email lưu trong session sau khi gửi mã
    //         → Copy URL sang trình duyệt khác = redirect (không có session)
    //  Lớp 2: OTP hash → Hash::make()
    //  Lớp 3: Expiry (10 phút) + Attempt limit (5 lần)

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Không tiết lộ email có tồn tại hay không (chống enumeration attack)
        if (!$user) {
            return back()->with('status', 'Nếu email tồn tại trong hệ thống, mã xác thực đã được gửi.')
                         ->withInput();
        }

        // Rate limit: 60 giây
        $lastSent = DB::table('password_resets')
            ->where('email', $user->email)
            ->value('created_at');

        if ($lastSent && Carbon::parse($lastSent)->diffInSeconds(now()) < 60) {
            $waitSeconds = 60 - Carbon::parse($lastSent)->diffInSeconds(now());
            return back()->withErrors([
                'email' => "Vui lòng đợi {$waitSeconds} giây trước khi gửi lại.",
            ])->withInput();
        }

        // Xóa mã cũ → tạo mã mới
        DB::table('password_resets')->where('email', $user->email)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_resets')->insert([
            'email'      => $user->email,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($user->email)->send(
                new PasswordResetCodeMail($code, $user->ho_ten)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Mail send failed: " . $e->getMessage());
            return back()->withErrors(['email' => 'Hệ thống gửi mail đang lỗi, vui lòng thử lại sau.'])->withInput();
        }

        // Lưu email vào session (Security Lớp 1)
        $request->session()->put('password_reset_email', $user->email);

        return redirect()->route('password.reset')
            ->with('status', 'Mã xác thực đã được gửi đến email của bạn!');
    }

    public function showResetPassword(Request $request)
    {
        // Security Lớp 1: session phải có email
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('auth.forgot-password')
                ->withErrors(['email' => 'Vui lòng nhập email trước.']);
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Bước 1: Xác thực mã OTP (chỉ nhập mã, chưa đổi mật khẩu).
     * Route: POST /reset-password
     */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'otp'   => ['required', 'array', 'size:6'],
            'otp.*' => ['required', 'string', 'size:1'],
        ], [
            'otp.required' => 'Vui lòng nhập mã xác thực.',
        ]);

        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('auth.forgot-password')
                ->withErrors(['email' => 'Phiên đã hết hạn. Vui lòng nhập email lại.']);
        }

        $inputCode = implode('', $request->otp);

        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$reset) {
            return back()->withErrors(['otp' => 'Bạn chưa có mã. Vui lòng quay lại gửi mã.']);
        }

        if (Carbon::parse($reset->expires_at)->isPast()) {
            DB::table('password_resets')->where('email', $email)->delete();
            $request->session()->forget('password_reset_email');
            return redirect()->route('auth.forgot-password')
                ->withErrors(['email' => 'Mã đã hết hạn. Vui lòng gửi mã mới.']);
        }

        if ($reset->attempts >= 5) {
            DB::table('password_resets')->where('email', $email)->delete();
            $request->session()->forget('password_reset_email');
            return redirect()->route('auth.forgot-password')
                ->withErrors(['email' => 'Nhập sai quá 5 lần. Mã đã bị hủy.']);
        }

        if (!Hash::check($inputCode, $reset->code_hash)) {
            $attemptsLeft = 4 - $reset->attempts;
            DB::table('password_resets')->where('email', $email)->increment('attempts');
            return back()->withErrors(['otp' => "Mã không đúng. Bạn còn {$attemptsLeft} lần thử."]);
        }

        // ✅ MÃ ĐÚNG → Xóa mã khỏi DB + đánh dấu session đã xác thực
        DB::table('password_resets')->where('email', $email)->delete();
        $request->session()->put('password_reset_verified', true);

        return redirect()->route('password.new');
    }

    /**
     * Bước 2a: Hiển thị form nhập mật khẩu mới.
     * Route: GET /new-password
     * Chỉ truy cập được SAU KHI đã xác thực mã OTP (session flag).
     */
    public function showNewPassword(Request $request)
    {
        if (!$request->session()->get('password_reset_verified')) {
            return redirect()->route('auth.forgot-password')
                ->withErrors(['email' => 'Vui lòng xác thực mã trước.']);
        }

        return view('auth.new-password');
    }

    /**
     * Bước 2b: Xử lý đổi mật khẩu.
     * Route: POST /new-password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $email = $request->session()->get('password_reset_email');
        $verified = $request->session()->get('password_reset_verified');

        if (!$email || !$verified) {
            return redirect()->route('auth.forgot-password')
                ->withErrors(['email' => 'Phiên đã hết hạn. Vui lòng thực hiện lại.']);
        }

        // ✅ Đổi mật khẩu
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa tất cả các thiết bị/phiên làm việc cũ khi đổi mật khẩu (bắt buộc đăng nhập lại)
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // Xóa session
        $request->session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('login')
            ->with('success', 'Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.');
    }

    public function resendResetCode(Request $request)
    {
        $email = $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('auth.forgot-password');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('auth.forgot-password');
        }

        // Rate limit: 60 giây
        $lastSent = DB::table('password_resets')
            ->where('email', $email)
            ->value('created_at');

        if ($lastSent && Carbon::parse($lastSent)->diffInSeconds(now()) < 60) {
            $waitSeconds = 60 - Carbon::parse($lastSent)->diffInSeconds(now());
            return back()->with('resend_error', "Vui lòng đợi {$waitSeconds} giây trước khi gửi lại.");
        }

        DB::table('password_resets')->where('email', $email)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_resets')->insert([
            'email'      => $email,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($email)->send(
                new PasswordResetCodeMail($code, $user->ho_ten)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Mail send failed: " . $e->getMessage());
            return back()->with('resend_error', 'Hệ thống gửi mail đang lỗi, vui lòng thử lại sau.');
        }

        return back()->with('resend_success', 'Mã mới đã được gửi đến email của bạn!');
    }

    // =====================================================================
    //  PRIVATE HELPERS
    // =====================================================================

    private function sendVerificationCode(User $user): void
    {
        DB::table('email_verifications')->where('user_id', $user->id)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('email_verifications')->insert([
            'user_id'    => $user->id,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($user->email)->send(
                new VerificationCodeMail($code, $user->ho_ten)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Mail send failed: " . $e->getMessage());
        }
    }
}
