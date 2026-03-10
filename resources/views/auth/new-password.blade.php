@extends('layouts.auth')

@section('title', 'Mật khẩu mới')

@section('content')
    <div style="text-align: center;">
        <div class="status-icon success" style="margin: 0 auto var(--space-6);">
            <span class="material-icons" style="font-size: 40px;">verified</span>
        </div>

        <h2>Tạo mật khẩu mới</h2>
        <p class="auth-subtitle" style="max-width: 360px; margin: 0 auto var(--space-6);">
            Mã xác thực hợp lệ! Hãy nhập mật khẩu mới cho tài khoản của bạn.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" id="new-password-form">
        @csrf

        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu mới</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Tối thiểu 8 ký tự" required autofocus>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                   placeholder="Nhập lại mật khẩu mới" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-save-password">Đặt lại mật khẩu</button>
    </form>

    <div style="text-align: center; margin-top: var(--space-6);">
        <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
            <span class="material-icons" style="font-size: 18px;">arrow_back</span>
            Quay lại Đăng nhập
        </a>
    </div>
@endsection
