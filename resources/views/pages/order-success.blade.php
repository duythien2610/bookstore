@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="container">
        <div class="status-page" id="order-success">
            <div class="status-icon success">
                <span class="material-icons">check_circle</span>
            </div>
            <h2>Đặt hàng thành công!</h2>
            <p>Cảm ơn bạn đã mua sắm tại Modtra Books. Đơn hàng <strong>#MB20240001</strong> đã được xác nhận.</p>

            <div style="background: var(--color-bg); border-radius: var(--radius-xl); padding: var(--space-6); text-align: left; margin-bottom: var(--space-8); max-width: 100%;">
                <h4 style="margin-bottom: var(--space-4);">Thông tin đơn hàng</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-3); font-size: var(--font-size-sm);">
                    <div><span style="color: var(--color-text-muted);">Mã đơn hàng:</span></div>
                    <div style="font-weight: var(--font-semibold);">#MB20240001</div>
                    <div><span style="color: var(--color-text-muted);">Ngày đặt:</span></div>
                    <div>{{ now()->format('d/m/Y H:i') }}</div>
                    <div><span style="color: var(--color-text-muted);">Phương thức:</span></div>
                    <div>Thanh toán khi nhận hàng</div>
                    <div><span style="color: var(--color-text-muted);">Tổng cộng:</span></div>
                    <div style="font-weight: var(--font-bold); color: var(--color-primary-dark);">736.000đ</div>
                </div>
            </div>

            <div style="display: flex; gap: var(--space-4); justify-content: center;">
                <a href="{{ url('/order-tracking') }}" class="btn btn-primary btn-lg" id="btn-track-order">
                    <span class="material-icons">local_shipping</span>
                    Theo dõi đơn hàng
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline btn-lg" id="btn-continue-shopping">Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>
@endsection
