@extends('layouts.auth')

@section('title', 'Đăng nhập')

@section('content')
    <h2>Chào mừng trở lại</h2>
    <p class="auth-subtitle">Khám phá thế giới tri thức cùng Modtra Books</p>

    {{-- Social Login --}}
    <div class="social-login">
        <button type="button" class="btn btn-outline" id="btn-google-login">
            <span class="material-icons">g_translate</span>
            Google
        </button>
        <button type="button" class="btn btn-outline" id="btn-facebook-login">
            <span class="material-icons">facebook</span>
            Facebook
        </button>
    </div>

    <div class="divider">hoặc đăng nhập bằng email</div>

    {{-- Login Form --}}
    <form method="POST" action="{{ url('/login') }}" id="login-form">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="example@email.com" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>
            <a href="{{ url('/forgot-password') }}" style="font-size: var(--font-size-sm); font-weight: var(--font-medium);">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-login-submit">Đăng nhập</button>
    </form>

    <p class="auth-footer" style="border: none; padding: 0; margin-top: var(--space-6);">
        Chưa có tài khoản? <a href="{{ url('/register') }}">Đăng ký ngay</a>
    </p>

    <p style="text-align: center; font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: var(--space-4);">
        Bằng cách tiếp tục, bạn đồng ý với
        <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a> của chúng tôi.
    </p>
@endsection
