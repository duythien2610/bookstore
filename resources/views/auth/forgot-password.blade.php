@extends('layouts.auth')

@section('title', 'Quên mật khẩu')

@section('content')
    <h2>Quên mật khẩu?</h2>
    <p class="auth-subtitle">Đừng lo lắng! Nhập email của bạn để nhận mã xác thực khôi phục mật khẩu.</p>

    @if (session('status'))
        <div class="badge badge-success" style="width: 100%; justify-content: center; padding: var(--space-3); margin-bottom: var(--space-6);">
            <span class="material-icons" style="font-size: 16px; margin-right: var(--space-2);">check_circle</span>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ url('/forgot-password') }}" id="forgot-password-form">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Địa chỉ email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   placeholder="example@email.com" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-send-reset">Gửi mã xác thực</button>
    </form>

    <div style="text-align: center; margin-top: var(--space-6);">
        <a href="{{ url('/login') }}" style="display: inline-flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
            <span class="material-icons" style="font-size: 18px;">arrow_back</span>
            Quay lại Đăng nhập
        </a>
    </div>
@endsection
