@extends('layouts.auth')

@section('title', 'Xác thực email')

@section('content')
    <div style="text-align: center;">
        <div class="status-icon info" style="margin: 0 auto var(--space-6);">
            <span class="material-icons" style="font-size: 40px;">mark_email_read</span>
        </div>

        <h2>Xác thực email</h2>
        <p class="auth-subtitle" style="max-width: 360px; margin: 0 auto var(--space-8);">
            Chúng tôi đã gửi mã xác thực đến email <strong>{{ $email ?? 'your@email.com' }}</strong>. Vui lòng nhập mã bên dưới.
        </p>

        <form method="POST" action="{{ url('/verify-email') }}" id="verify-email-form">
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

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-verify-email">Xác thực</button>
        </form>

        <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-top: var(--space-6);">
            Không nhận được mã?
            <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();" style="font-weight: var(--font-semibold);">
                Gửi lại mã
            </a>
        </p>

        <form id="resend-form" method="POST" action="{{ url('/verify-email/resend') }}" style="display: none;">
            @csrf
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-focus next OTP input
    document.querySelectorAll('.otp-inputs input').forEach((input, index, inputs) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
</script>
@endpush
