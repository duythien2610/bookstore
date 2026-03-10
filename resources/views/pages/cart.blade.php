@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Giỏ hàng</span>
            </div>
            <h1>Giỏ hàng của bạn</h1>
        </div>
    </div>

    <div class="container">
        <div class="cart-grid" id="cart-content">
            {{-- Cart Items --}}
            <div>
                @for ($i = 1; $i <= 3; $i++)
                <div class="cart-item" id="cart-item-{{ $i }}">
                    <div class="cart-item-img" style="display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 40px; color: var(--color-text-muted);">book</span>
                    </div>
                    <div class="cart-item-info">
                        <div>
                            <h4>Tên sách mẫu {{ $i }}</h4>
                            <p class="author">Tác giả {{ $i }}</p>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="quantity-control">
                                <button type="button">−</button>
                                <span>{{ $i }}</span>
                                <button type="button">+</button>
                            </div>
                            <span style="font-weight: var(--font-bold); font-size: var(--font-size-lg); color: var(--color-primary-dark);">
                                {{ number_format($i * 189000, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                    <button style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); align-self: start; padding: var(--space-2);" title="Xóa">
                        <span class="material-icons">close</span>
                    </button>
                </div>
                @endfor
            </div>

            {{-- Order Summary --}}
            <div class="order-summary" id="order-summary">
                <h3>Tóm tắt đơn hàng</h3>
                <div class="summary-row">
                    <span>Tạm tính (3 sản phẩm)</span>
                    <span>756.000đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span>30.000đ</span>
                </div>
                <div class="summary-row">
                    <span style="color: var(--color-primary-dark);">Giảm giá</span>
                    <span style="color: var(--color-primary-dark);">-50.000đ</span>
                </div>
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span>736.000đ</span>
                </div>

                <div class="form-group" style="margin-top: var(--space-6);">
                    <div style="display: flex; gap: var(--space-2);">
                        <input type="text" class="form-control" placeholder="Mã giảm giá" id="coupon-input">
                        <button class="btn btn-outline" id="btn-apply-coupon">Áp dụng</button>
                    </div>
                </div>

                <a href="{{ url('/checkout') }}" class="btn btn-primary btn-block btn-lg" id="btn-checkout">
                    <span class="material-icons">lock</span>
                    Thanh toán
                </a>

                <a href="{{ url('/products') }}" style="display: block; text-align: center; margin-top: var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
                    <span class="material-icons" style="font-size: 14px; vertical-align: middle;">arrow_back</span>
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
    </div>
@endsection
