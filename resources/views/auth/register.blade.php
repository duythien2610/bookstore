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
    <p class="auth-subtitle">Tham gia cộng đồng đọc sách Modtra Books</p>

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

    <p class="auth-footer" style="border: none; padding: 0; margin-top: var(--space-6);">
        Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
    </p>
@endsection
