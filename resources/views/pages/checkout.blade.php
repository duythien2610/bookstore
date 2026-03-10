@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <a href="{{ url('/cart') }}">Giỏ hàng</a>
                <span class="separator">›</span>
                <span>Thanh toán</span>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Checkout Steps --}}
        <div class="checkout-steps" id="checkout-steps">
            <div class="checkout-step completed">
                <span class="step-num"><span class="material-icons" style="font-size: 16px;">check</span></span>
                Giỏ hàng
            </div>
            <div class="step-line active"></div>
            <div class="checkout-step active">
                <span class="step-num">2</span>
                Thanh toán
            </div>
            <div class="step-line"></div>
            <div class="checkout-step">
                <span class="step-num">3</span>
                Hoàn tất
            </div>
        </div>

        <div class="cart-grid">
            {{-- Checkout Form --}}
            <div>
                <form method="POST" action="{{ url('/checkout') }}" id="checkout-form">
                    @csrf

                    {{-- Shipping Info --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
                        <h3 style="margin-bottom: var(--space-6);">Thông tin giao hàng</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                            <div class="form-group">
                                <label for="fullname" class="form-label">Họ và tên</label>
                                <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="0912 345 678" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="Số nhà, đường..." required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: var(--space-4);">
                            <div class="form-group">
                                <label for="city" class="form-label">Tỉnh/Thành phố</label>
                                <select id="city" name="city" class="form-control" required>
                                    <option value="">Chọn tỉnh/thành</option>
                                    <option>Hà Nội</option>
                                    <option>TP. Hồ Chí Minh</option>
                                    <option>Đà Nẵng</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="district" class="form-label">Quận/Huyện</label>
                                <select id="district" name="district" class="form-control" required>
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ward" class="form-label">Phường/Xã</label>
                                <select id="ward" name="ward" class="form-control" required>
                                    <option value="">Chọn phường/xã</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea id="note" name="note" class="form-control" rows="3" placeholder="Ghi chú cho đơn hàng..."></textarea>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light);">
                        <h3 style="margin-bottom: var(--space-6);">Phương thức thanh toán</h3>

                        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                            <label style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4); border: 2px solid var(--color-primary); border-radius: var(--radius-lg); cursor: pointer; background: var(--color-primary-light);">
                                <input type="radio" name="payment" value="cod" checked>
                                <span class="material-icons" style="color: var(--color-primary-dark);">local_shipping</span>
                                <div>
                                    <div style="font-weight: var(--font-semibold); font-size: var(--font-size-sm);">Thanh toán khi nhận hàng (COD)</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Thanh toán bằng tiền mặt khi nhận sách</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4); border: 1px solid var(--color-border); border-radius: var(--radius-lg); cursor: pointer;">
                                <input type="radio" name="payment" value="bank">
                                <span class="material-icons" style="color: var(--color-text-secondary);">account_balance</span>
                                <div>
                                    <div style="font-weight: var(--font-semibold); font-size: var(--font-size-sm);">Chuyển khoản ngân hàng</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Chuyển khoản qua ngân hàng nội địa</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4); border: 1px solid var(--color-border); border-radius: var(--radius-lg); cursor: pointer;">
                                <input type="radio" name="payment" value="momo">
                                <span class="material-icons" style="color: var(--color-text-secondary);">phone_iphone</span>
                                <div>
                                    <div style="font-weight: var(--font-semibold); font-size: var(--font-size-sm);">Ví MoMo</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Thanh toán qua ví điện tử MoMo</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="order-summary" id="checkout-summary">
                <h3>Đơn hàng của bạn</h3>
                @for ($i = 1; $i <= 3; $i++)
                <div style="display: flex; gap: var(--space-3); margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px solid var(--color-border-light);">
                    <div style="width: 60px; height: 80px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span class="material-icons" style="color: var(--color-text-muted);">book</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: var(--font-size-sm); font-weight: var(--font-medium);">Tên sách {{ $i }}</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">x{{ $i }}</div>
                    </div>
                    <div style="font-size: var(--font-size-sm); font-weight: var(--font-semibold);">{{ number_format($i * 189000, 0, ',', '.') }}đ</div>
                </div>
                @endfor

                <div class="summary-row"><span>Tạm tính</span><span>756.000đ</span></div>
                <div class="summary-row"><span>Phí vận chuyển</span><span>30.000đ</span></div>
                <div class="summary-row"><span style="color: var(--color-primary-dark);">Giảm giá</span><span style="color: var(--color-primary-dark);">-50.000đ</span></div>
                <div class="summary-row total"><span>Tổng cộng</span><span>736.000đ</span></div>

                <button type="submit" form="checkout-form" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-6);" id="btn-place-order">
                    Đặt hàng
                </button>
            </div>
        </div>
    </div>
@endsection
