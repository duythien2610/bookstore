@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <a href="{{ route('cart') }}">Giỏ hàng</a>
                <span class="separator">›</span>
                <span>Thanh toán</span>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Steps --}}
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

        @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: var(--space-4);">
            <ul style="margin: 0; padding-left: var(--space-4);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="cart-grid">
            {{-- Form --}}
            <div>
                <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ (string) Str::uuid() }}">
                    
                    {{-- Honeypot Trap cho Bots --}}
                    <div style="display: none; visibility: hidden; position: absolute; left: -9999px;">
                        <label for="website_url">Vui lòng để trống trường này nếu bạn là người</label>
                        <input type="text" name="website_url" id="website_url" value="" autocomplete="off" tabindex="-1">
                    </div>

                    {{-- Shipping Info --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
                        <h3 style="margin-bottom: var(--space-6);">Thông tin giao hàng</h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4);">
                            <div class="form-group">
                                <label for="ho_ten" class="form-label">Họ và tên *</label>
                                <input type="text" id="ho_ten" name="ho_ten" class="form-control"
                                    value="{{ old('ho_ten', $user->ho_ten) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="so_dien_thoai" class="form-label">Số điện thoại *</label>
                                <input type="tel" id="so_dien_thoai" name="so_dien_thoai" class="form-control"
                                    value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dia_chi" class="form-label">Địa chỉ giao hàng *</label>
                            <textarea id="dia_chi" name="dia_chi" class="form-control" rows="2"
                                placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố..." required>{{ old('dia_chi', $user->dia_chi ? str_replace('|', ', ', $user->dia_chi) : '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="ghi_chu" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="form-control" rows="2"
                                placeholder="Ghi chú cho đơn hàng, yêu cầu đặc biệt...">{{ old('ghi_chu') }}</textarea>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light);">
                        <h3 style="margin-bottom: var(--space-6);">Phương thức thanh toán</h3>

                        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                            <label id="label-cod" style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4); border: 2px solid var(--color-primary); border-radius: var(--radius-lg); cursor: pointer; background: var(--color-primary-light);">
                                <input type="radio" name="phuong_thuc_tt" value="cod" checked onchange="togglePaymentInfo(this.value)">
                                <span class="material-icons" style="color: var(--color-primary-dark);">local_shipping</span>
                                <div>
                                    <div style="font-weight: var(--font-semibold); font-size: var(--font-size-sm);">Thanh toán khi nhận hàng (COD)</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Thanh toán bằng tiền mặt khi nhận sách</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-4); border: 1px solid var(--color-border); border-radius: var(--radius-lg); cursor: pointer;">
                                <input type="radio" name="phuong_thuc_tt" value="payos" onchange="togglePaymentInfo(this.value)">
                                <span class="material-icons" style="color: var(--color-text-secondary);">qr_code_scanner</span>
                                <div>
                                    <div style="font-weight: var(--font-semibold); font-size: var(--font-size-sm);">Thanh toán qua PayOS (QR Code / Thẻ / Ngân hàng)</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">Thanh toán tự động 24/7 với VietQR</div>
                                </div>
                            </label>

                        </div>

                        {{-- PayOS Info --}}
                        <div id="payos-info" style="display: none; margin-top: var(--space-4); padding: var(--space-4); background: var(--color-bg-alt); border-radius: var(--radius-lg);">
                            <p style="font-size: var(--font-size-sm); color: var(--color-text-muted); margin-bottom: 0;">
                                Sau khi đặt hàng, bạn sẽ được chuyển hướng tới trang thanh toán an toàn của PayOS. Hệ thống tự động xác nhận đơn hàng ngay khi nhận được thanh toán.
                            </p>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="order-summary" id="checkout-summary">
                <h3>Đơn hàng của bạn</h3>
                @foreach ($cart as $key => $item)
                <div style="display: flex; gap: var(--space-3); margin-bottom: var(--space-4); padding-bottom: var(--space-4); border-bottom: 1px solid var(--color-border-light);">
                    <div style="width: 60px; height: 80px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                        @if (!empty($item['anh_bia']) && !str_starts_with($item['anh_bia'], 'http'))
                            <img src="{{ asset('uploads/books/' . $item['anh_bia']) }}" style="width:100%;height:100%;object-fit:cover;">
                        @elseif (!empty($item['anh_bia']))
                            <img src="{{ $item['anh_bia'] }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'">
                        @else
                            <span class="material-icons" style="color: var(--color-text-muted);">book</span>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: var(--font-size-sm); font-weight: var(--font-medium);">{{ Str::limit($item['tieu_de'], 50) }}</div>
                        <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">x{{ $item['so_luong'] }}</div>
                    </div>
                    <div style="font-size: var(--font-size-sm); font-weight: var(--font-semibold);">{{ number_format($item['gia_ban'] * $item['so_luong'], 0, ',', '.') }}đ</div>
                </div>
                @endforeach

                <div class="summary-row"><span>Tạm tính</span><span>{{ number_format($subtotal, 0, ',', '.') }}đ</span></div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span>{{ $phi_ship == 0 ? 'Miễn phí' : number_format($phi_ship, 0, ',', '.') . 'đ' }}</span>
                </div>
                @if ($discount > 0)
                <div class="summary-row">
                    <span style="color: var(--color-primary-dark);">Giảm giá</span>
                    <span style="color: var(--color-primary-dark);">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                </div>
                @endif
                <div class="summary-row total"><span>Tổng cộng</span><span>{{ number_format($total, 0, ',', '.') }}đ</span></div>

                <button type="submit" form="checkout-form" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-6);" id="btn-place-order">
                    <span class="material-icons">check_circle</span>
                    Đặt hàng ngay
                </button>

                <p style="font-size: var(--font-size-xs); color: var(--color-text-muted); text-align: center; margin-top: var(--space-3);">
                    <span class="material-icons" style="font-size: 12px; vertical-align: middle;">lock</span>
                    Thông tin được bảo mật tuyệt đối
                </p>
            </div>
        </div>
    </div>

@push('scripts')
<script>
function togglePaymentInfo(value) {
    const payosInfo = document.getElementById('payos-info');
    payosInfo.style.display = value === 'payos' ? 'block' : 'none';

    // Style các label
    document.querySelectorAll('[name="phuong_thuc_tt"]').forEach(radio => {
        const label = radio.closest('label');
        if (radio.checked) {
            label.style.border = '2px solid var(--color-primary)';
            label.style.background = 'var(--color-primary-light)';
        } else {
            label.style.border = '1px solid var(--color-border)';
            label.style.background = '';
        }
    });
}
</script>
@endpush
@endsection
