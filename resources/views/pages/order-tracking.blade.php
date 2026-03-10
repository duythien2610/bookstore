@extends('layouts.app')

@section('title', 'Theo dõi đơn hàng')

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Theo dõi đơn hàng</span>
            </div>
            <h1>Chi tiết đơn hàng #MB20240001</h1>
        </div>
    </div>

    <div class="container">
        <div class="cart-grid" id="order-tracking">
            {{-- Tracking Timeline --}}
            <div>
                <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6);">
                        <h3>Trạng thái đơn hàng</h3>
                        <span class="badge badge-info">Đang giao hàng</span>
                    </div>

                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <h4>Đơn hàng đã được đặt</h4>
                            <p>01/03/2026, 14:30</p>
                        </div>
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <h4>Đã xác nhận đơn hàng</h4>
                            <p>01/03/2026, 15:00</p>
                        </div>
                        <div class="timeline-item completed">
                            <div class="timeline-dot"></div>
                            <h4>Đang đóng gói</h4>
                            <p>01/03/2026, 16:45</p>
                        </div>
                        <div class="timeline-item active">
                            <div class="timeline-dot"></div>
                            <h4>Đang giao hàng</h4>
                            <p>Dự kiến giao: 03/03/2026</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <h4>Giao hàng thành công</h4>
                            <p>Chờ xác nhận</p>
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light);">
                    <h3 style="margin-bottom: var(--space-6);">Sản phẩm trong đơn</h3>
                    @for ($i = 1; $i <= 3; $i++)
                    <div style="display: flex; gap: var(--space-4); padding: var(--space-4) 0; {{ $i < 3 ? 'border-bottom: 1px solid var(--color-border-light);' : '' }}">
                        <div style="width: 70px; height: 90px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <span class="material-icons" style="color: var(--color-text-muted);">book</span>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: var(--font-medium); margin-bottom: var(--space-1);">Tên sách {{ $i }}</div>
                            <div style="font-size: var(--font-size-sm); color: var(--color-text-muted);">Tác giả {{ $i }} · x{{ $i }}</div>
                        </div>
                        <div style="font-weight: var(--font-semibold);">{{ number_format($i * 189000, 0, ',', '.') }}đ</div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Order Info Sidebar --}}
            <div class="order-summary" id="order-info">
                <h3>Thông tin đơn hàng</h3>
                <div style="font-size: var(--font-size-sm); display: flex; flex-direction: column; gap: var(--space-4);">
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Mã đơn hàng</div>
                        <div style="font-weight: var(--font-semibold);">#MB20240001</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Người nhận</div>
                        <div style="font-weight: var(--font-medium);">Nguyễn Văn A</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Số điện thoại</div>
                        <div>0912 345 678</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Địa chỉ giao hàng</div>
                        <div>123 Đường ABC, Quận 1, TP. Hồ Chí Minh</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Thanh toán</div>
                        <div>COD — Thanh toán khi nhận hàng</div>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--color-border-light); margin-top: var(--space-6); padding-top: var(--space-4);">
                    <div class="summary-row"><span>Tạm tính</span><span>756.000đ</span></div>
                    <div class="summary-row"><span>Phí vận chuyển</span><span>30.000đ</span></div>
                    <div class="summary-row"><span style="color: var(--color-primary-dark);">Giảm giá</span><span style="color: var(--color-primary-dark);">-50.000đ</span></div>
                    <div class="summary-row total"><span>Tổng cộng</span><span>736.000đ</span></div>
                </div>
            </div>
        </div>
    </div>
@endsection
