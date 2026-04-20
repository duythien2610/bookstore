@extends('layouts.auth')

@section('title', 'Xác thực thành công')

@section('content')
    <div class="status-page" style="padding: 0;">
        <div class="status-icon success">
            <span class="material-icons">check_circle</span>
        </div>

        <h2>Xác thực thành công!</h2>
        <p>Tài khoản của bạn đã được xác thực thành công. Bạn có thể bắt đầu khám phá hàng ngàn đầu sách chất lượng tại Bookverse.</p>

        <a href="{{ route('home') }}" class="btn btn-primary btn-lg" id="btn-go-shopping">
            <span class="material-icons">auto_stories</span>
            Bắt đầu mua sắm
        </a>

        <div style="margin-top: var(--space-6);">
            <a href="{{ route('profile') }}" style="font-size: var(--font-size-sm); color: var(--color-text-secondary);">
                hoặc cập nhật hồ sơ của bạn →
            </a>
        </div>
    </div>
@endsection
