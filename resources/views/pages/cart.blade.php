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
        @if($items->isEmpty())
            <div style="text-align: center; padding: var(--space-20) 0;">
                <span class="material-icons" style="font-size: 80px; color: var(--color-border); margin-bottom: var(--space-4);">shopping_basket</span>
                <h2>Giỏ hàng của bạn đang trống</h2>
                <p style="color: var(--color-text-muted); margin-bottom: var(--space-8);">Hãy khám phá hàng nghìn tựa sách hấp dẫn tại Modtra Books.</p>
                <a href="{{ url('/products') }}" class="btn btn-primary">Mua sắm ngay</a>
            </div>
        @else
        <div class="cart-grid" id="cart-content">
            {{-- Cart Items --}}
            <div>
                @foreach ($items as $item)
                <div class="cart-item" id="cart-item-{{ $item->id }}">
                    <div class="cart-item-img">
                        @php
                            $imageUrl = $item->sach->link_anh_bia ?: ($item->sach->file_anh_bia ? asset('uploads/books/' . $item->sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $item->sach->tieu_de }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-lg);">
                    </div>
                    <div class="cart-item-info">
                        <div>
                            <h4 style="margin-bottom: 4px;">{{ $item->sach->tieu_de }}</h4>
                            <p class="author">Tác giả: {{ $item->sach->tacGia ? $item->sach->tacGia->ten_tac_gia : 'Chưa cập nhật' }}</p>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                            {{-- Quantity Update --}}
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <div class="quantity-control">
                                    <button type="submit" name="so_luong" value="{{ $item->so_luong - 1 }}" {{ $item->so_luong <= 1 ? 'disabled' : '' }}>−</button>
                                    <span>{{ $item->so_luong }}</span>
                                    <button type="submit" name="so_luong" value="{{ $item->so_luong + 1 }}">+</button>
                                </div>
                            </form>
                            <span style="font-weight: var(--font-bold); font-size: var(--font-size-lg); color: var(--color-primary-dark);">
                                {{ number_format($item->thanh_tien, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                    {{-- Remove Item --}}
                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); align-self: start; padding: var(--space-2);" title="Xóa">
                            <span class="material-icons">close</span>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>

            {{-- Order Summary --}}
            <div class="order-summary" id="order-summary">
                <h3>Tóm tắt đơn hàng</h3>
                {{-- Coupon Input --}}
                <div style="background: var(--color-bg-alt); padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6);">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Mã giảm giá</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="coupon-input" value="{{ $couponCode }}" placeholder="Nhập mã..." style="flex: 1; height: 40px; border: 1px solid var(--color-border); border-radius: 8px; padding: 0 12px;">
                        <button type="button" id="btn-apply-coupon" class="btn btn-outline" style="height: 40px; padding: 0 var(--space-4);">Áp dụng</button>
                    </div>
                    <div id="coupon-message" style="margin-top: 8px; font-size: 12px; display: none;"></div>
                </div>

                <div class="summary-row">
                    <span>Tạm tính ({{ $items->count() }} sản phẩm)</span>
                    <span>{{ number_format($gioHang->tong_tien, 0, ',', '.') }}đ</span>
                </div>
                <div class="summary-row" id="discount-row" @if(!$discount) style="display: none;" @endif>
                    <span style="color: var(--color-primary-dark);">Giảm giá</span>
                    <span style="color: var(--color-primary-dark);" id="discount-amount">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span style="color: var(--color-success);">Miễn phí</span>
                </div>
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span style="color: var(--color-text); font-size: 24px;" id="final-total">{{ number_format($gioHang->tong_tien - $discount, 0, ',', '.') }}đ</span>
                </div>

                <a href="{{ url('/checkout') }}" class="btn btn-primary btn-block btn-lg" id="btn-checkout" style="margin-top: var(--space-8);">
                    <span class="material-icons">lock</span>
                    Thanh toán bảo mật
                </a>

                <a href="{{ url('/products') }}" style="display: block; text-align: center; margin-top: var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
        @endif
    </div>
    <script>
        document.getElementById('btn-apply-coupon').addEventListener('click', function() {
            const coupon = document.getElementById('coupon-input').value;
            if (!coupon) return;

            const btn = this;
            const messageEl = document.getElementById('coupon-message');
            btn.disabled = true;

            fetch('{{ route("cart.coupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ coupon: coupon })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                messageEl.style.display = 'block';
                messageEl.textContent = data.message;
                messageEl.style.color = data.success ? 'var(--color-success)' : 'var(--color-danger)';

                if (data.success) {
                    document.getElementById('discount-row').style.display = 'flex';
                    document.getElementById('discount-amount').textContent = '-' + new Intl.NumberFormat('vi-VN').format(data.discount) + 'đ';
                    document.getElementById('final-total').textContent = new Intl.NumberFormat('vi-VN').format(data.final_total) + 'đ';
                }
            })
            .catch(err => {
                btn.disabled = false;
                console.error(err);
            });
        });
    </script>
@endsection
