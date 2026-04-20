@extends('layouts.auth')

@section('title', 'Đăng ký')

@section('content')
    @if ($errors->any())
        <div class="badge badge-danger" style="width: 100%; justify-content: center; padding: var(--space-3); margin-bottom: var(--space-6);">
            <span class="material-icons" style="font-size: 16px; margin-right: var(--space-2);">error</span>
            Vui lòng kiểm tra lại thông tin đăng ký.
        </div>
    @endif

    <h2>Tạo tài khoản mới</h2>
    <p class="auth-subtitle">Tham gia cộng đồng đọc sách Bookverse</p>

    {{-- Register Form --}}
    <form method="POST" action="{{ route('register') }}" id="register-form">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Họ và tên</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                   placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="example@email.com" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   placeholder="0912 345 678" value="{{ old('phone') }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Tối thiểu 8 ký tự" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                   placeholder="Nhập lại mật khẩu" required>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="agree" name="agree" required>
                <label for="agree">
                    Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-register-submit">Đăng ký</button>
    </form>

    <div style="margin: var(--space-6) 0; display: flex; align-items: center; gap: var(--space-4);">
        <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
        <div style="font-size: var(--font-size-xs); color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Hoặc tiếp tục với</div>
        <div style="flex: 1; height: 1px; background: var(--color-border);"></div>
    </div>

    <a href="{{ route('auth.google') }}" class="btn btn-block" style="background: white; border: 1px solid #d1d5db; color: #374151; display: inline-flex; align-items: center; justify-content: center; gap: 12px; height: 48px; font-weight: 500; transition: all 0.2s;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Đăng ký bằng Google
    </a>

    <p class="auth-footer" style="border: none; padding: 0; margin-top: var(--space-6);">
        Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
    </p>
@endsection
