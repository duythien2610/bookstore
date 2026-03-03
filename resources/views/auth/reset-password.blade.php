@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <h2>Đặt lại mật khẩu</h2>
    <p class="auth-subtitle">Nhập mật khẩu mới cho tài khoản của bạn.</p>

    <form method="POST" action="{{ url('/reset-password') }}" id="reset-password-form">
        @csrf

        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="example@email.com" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu mới</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="Tối thiểu 8 ký tự" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                   placeholder="Nhập lại mật khẩu mới" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-reset-password">Đặt lại mật khẩu</button>
    </form>

    <div style="text-align: center; margin-top: var(--space-6);">
        <a href="{{ url('/login') }}" style="display: inline-flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
            <span class="material-icons" style="font-size: 18px;">arrow_back</span>
            Quay lại Đăng nhập
        </a>
    </div>
@endsection
