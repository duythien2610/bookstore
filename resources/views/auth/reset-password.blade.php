@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <div style="text-align: center;">
        <div class="status-icon info" style="margin: 0 auto var(--space-6);">
            <span class="material-icons" style="font-size: 40px;">lock_reset</span>
        </div>

        <h2>Nhập mã xác thực</h2>
        <p class="auth-subtitle" style="max-width: 360px; margin: 0 auto var(--space-6);">
            Nhập mã 6 số đã gửi về <strong>{{ $email }}</strong>
        </p>

        {{-- Flash messages --}}
        @if (session('status'))
            <div class="badge badge-success" style="width: 100%; justify-content: center; padding: var(--space-3); margin-bottom: var(--space-4);">
                <span class="material-icons" style="font-size: 16px; margin-right: var(--space-2);">check_circle</span>
                {{ session('status') }}
            </div>
        @endif

        @if (session('resend_success'))
            <div class="badge badge-success" style="width: 100%; justify-content: center; padding: var(--space-3); margin-bottom: var(--space-4);">
                <span class="material-icons" style="font-size: 16px; margin-right: var(--space-2);">check_circle</span>
                {{ session('resend_success') }}
            </div>
        @endif

        @if (session('resend_error'))
            <div class="badge badge-danger" style="width: 100%; justify-content: center; padding: var(--space-3); margin-bottom: var(--space-4);">
                <span class="material-icons" style="font-size: 16px; margin-right: var(--space-2);">error</span>
                {{ session('resend_error') }}
            </div>
        @endif
    </div>

    {{-- Bước 1: Chỉ nhập mã OTP --}}
    <form method="POST" action="{{ route('password.verify-code') }}" id="verify-form" class="otp-form">
        @csrf

        <div class="otp-inputs">
            <input type="text" maxlength="1" id="otp-1" name="otp[]" autofocus required>
            <input type="text" maxlength="1" id="otp-2" name="otp[]" required>
            <input type="text" maxlength="1" id="otp-3" name="otp[]" required>
            <input type="text" maxlength="1" id="otp-4" name="otp[]" required>
            <input type="text" maxlength="1" id="otp-5" name="otp[]" required>
            <input type="text" maxlength="1" id="otp-6" name="otp[]" required>
        </div>

        @error('otp')
            <div class="invalid-feedback" style="text-align: center; margin-bottom: var(--space-4);">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-verify-reset">Xác thực mã</button>
    </form>

    <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-top: var(--space-6); text-align: center;">
        Không nhận được mã?
        <a href="#" onclick="event.preventDefault(); document.getElementById('resend-reset-form').submit();" style="font-weight: var(--font-semibold);">
            Gửi lại mã
        </a>
    </p>

    <form id="resend-reset-form" method="POST" action="{{ route('password.resend') }}" style="display: none;">
        @csrf
    </form>

    <div style="text-align: center; margin-top: var(--space-4);">
        <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: var(--space-2); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
            <span class="material-icons" style="font-size: 18px;">arrow_back</span>
            Quay lại Đăng nhập
        </a>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.otp-inputs input').forEach((input, index, inputs) => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            pastedData.split('').forEach((char, i) => {
                if (inputs[index + i]) inputs[index + i].value = char;
            });
            const lastIndex = Math.min(index + pastedData.length - 1, inputs.length - 1);
            inputs[lastIndex].focus();
        });
    });
</script>
@endpush
