@extends('layouts.app')

@section('title', 'Giỏ hàng')

@push('styles')
<style>
.custom-checkbox {
    width: 18px;
    height: 18px;
    accent-color: var(--color-primary);
    cursor: pointer;
}
</style>
@endpush

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
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: var(--space-4);">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: var(--space-4);">{{ session('error') }}</div>
        @endif

        @if(empty($cart))
        {{-- Giỏ trống --}}
        <div style="text-align: center; padding: var(--space-20) 0;" id="cart-content">
            <span class="material-icons" style="font-size: 80px; color: var(--color-text-muted);">shopping_cart</span>
            <h2 style="margin-top: var(--space-4); color: var(--color-text-muted);">Giỏ hàng của bạn đang trống</h2>
            <p style="color: var(--color-text-muted); margin-top: var(--space-2);">Hãy khám phá và thêm sách vào giỏ hàng!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg" style="margin-top: var(--space-6);">
                <span class="material-icons">auto_stories</span> Khám phá sách
            </a>
        </div>
        @else

        <form action="{{ route('checkout.prepare') }}" method="POST" id="checkout-prepare-form">
            @csrf
            <div class="cart-grid" id="cart-content">
                {{-- Cart Items --}}
                <div id="cart-items-list">
                    {{-- Checkbox chọn tất cả --}}
                    <div style="padding: var(--space-4); background: var(--color-white); border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); margin-bottom: var(--space-4); display: flex; align-items: center; gap: var(--space-3);">
                        <input type="checkbox" id="check-all" class="custom-checkbox" checked onchange="toggleAllCheckboxes(this)">
                        <label for="check-all" style="font-weight: 600; cursor: pointer; user-select: none;">Chọn tất cả (<span id="selected-count">{{ count($cart) }}</span> sản phẩm)</label>
                    </div>

                    @foreach ($cart as $key => $item)
                    <div class="cart-item" id="cart-item-{{ $key }}" style="display: flex; align-items: center; gap: var(--space-4);">
                        <input type="checkbox" name="selected_items[]" value="{{ $key }}" class="item-checkbox custom-checkbox" checked onchange="recalculateTotal()" data-price="{{ $item['gia_ban'] }}" data-qty="{{ $item['so_luong'] }}">

                        <div class="cart-item-img" style="display: flex; align-items: center; justify-content: center; overflow: hidden; background: var(--color-bg-alt); border-radius: var(--radius-md); width: 80px; height: 110px; flex-shrink: 0;">
                            @if (!empty($item['anh_bia']) && !str_starts_with($item['anh_bia'], 'http'))
                                <img src="{{ asset('uploads/books/' . $item['anh_bia']) }}" alt="{{ $item['tieu_de'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @elseif (!empty($item['anh_bia']))
                                <img src="{{ $item['anh_bia'] }}" alt="{{ $item['tieu_de'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'">
                            @else
                                <span class="material-icons" style="font-size: 40px; color: var(--color-text-muted);">book</span>
                            @endif
                        </div>
                        <div class="cart-item-info" style="flex: 1;">
                            <div>
                                <a href="{{ route('products.show', $item['sach_id']) }}" style="text-decoration: none;">
                                    <h4 style="color: var(--color-text-primary); margin:0 0 4px 0;">{{ $item['tieu_de'] }}</h4>
                                </a>
                                <p class="author" style="margin:0 0 4px 0;">{{ $item['ten_tac_gia'] ?? 'Vô danh' }}</p>
                                <p style="font-size: var(--font-size-sm); color: var(--color-primary-dark); font-weight: var(--font-semibold); margin:0;">
                                    {{ number_format($item['gia_ban'], 0, ',', '.') }}đ / cuốn
                                </p>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                <div class="quantity-control">
                                    <button type="button" onclick="updateQty('{{ $key }}', -1)">−</button>
                                    <span id="qty-{{ $key }}">{{ $item['so_luong'] }}</span>
                                    <button type="button" onclick="updateQty('{{ $key }}', 1)">+</button>
                                </div>
                                <span id="subtotal-{{ $key }}" style="font-weight: var(--font-bold); font-size: var(--font-size-lg); color: var(--color-primary-dark);">
                                    {{ number_format($item['gia_ban'] * $item['so_luong'], 0, ',', '.') }}đ
                                </span>
                            </div>
                        </div>
                        <button type="button" onclick="removeItem('{{ $key }}')" style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); align-self: start; padding: var(--space-2);" title="Xóa">
                            <span class="material-icons">close</span>
                        </button>
                    </div>
                    @endforeach
                </div>

            {{-- Order Summary --}}
            <div class="order-summary" id="order-summary">
                <h3>Tóm tắt đơn hàng</h3>
                <div class="summary-row">
                    <span>Tạm tính (<span id="item-count">{{ count($cart) }}</span> sản phẩm)</span>
                    <span id="summary-subtotal">{{ number_format($total, 0, ',', '.') }}đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span>{{ $total >= 300000 ? 'Miễn phí' : '30.000đ' }}</span>
                </div>
                @if ($discount > 0)
                <div class="summary-row">
                    <span style="color: var(--color-primary-dark);">Giảm giá ({{ $couponCode }})</span>
                    <span style="color: var(--color-primary-dark);">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span id="summary-total">{{ number_format(max(0, $total - $discount) + ($total >= 300000 ? 0 : 30000), 0, ',', '.') }}đ</span>
                </div>

                {{-- Mã giảm giá --}}
                <div class="form-group" style="margin-top: var(--space-6);">
                    <div style="display: flex; gap: var(--space-2);">
                        <input type="text" class="form-control" name="coupon" placeholder="Mã giảm giá" id="coupon-input" value="{{ $couponCode ?? '' }}">
                        <button type="button" class="btn btn-outline" id="btn-apply-coupon" onclick="applyCoupon()">Áp dụng</button>
                    </div>
                    <div id="coupon-msg" style="font-size: var(--font-size-xs); margin-top: var(--space-2);"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" id="btn-checkout">
                    <span class="material-icons">lock</span>
                    Mua hàng
                </button>

                <a href="{{ route('products.index') }}" style="display: block; text-align: center; margin-top: var(--space-4); font-size: var(--font-size-sm); color: var(--color-text-secondary);">
                    <span class="material-icons" style="font-size: 14px; vertical-align: middle;">arrow_back</span>
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>
        </form>
        @endif
    </div>

@push('scripts')
<script>
const cartData = JSON.parse('{!! addslashes(json_encode($cart)) !!}');
const csrfToken = '{{ csrf_token() }}';

let baseDiscount = Number('{{ $discount ?? 0 }}');

function formatVND(n) {
    return new Intl.NumberFormat('vi-VN').format(n) + 'đ';
}

function updateQty(key, delta) {
    const qtyEl = document.getElementById('qty-' + key);
    const cbEl = document.querySelector(`input.item-checkbox[value="${key}"]`);
    const current = parseInt(qtyEl.textContent);
    const newQty  = current + delta;
    if (newQty < 1) return;

    fetch(`/cart/${key}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-Method-Override': 'PUT',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ so_luong: newQty, _method: 'PUT' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            qtyEl.textContent = newQty;
            cbEl.setAttribute('data-qty', newQty); // update DOM attribute for recount
            document.getElementById('subtotal-' + key).textContent = formatVND(data.item_total);
            recalculateTotal();
        }
    })
    .catch(() => location.reload());
}

function removeItem(key) {
    fetch(`/cart/${key}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'DELETE', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-item-' + key)?.remove();
            if (data.is_empty) location.reload();
            recalculateTotal();
        }
    })
    .catch(() => location.reload());
}

function applyCoupon() {
    const code = document.getElementById('coupon-input').value.trim();
    if (!code) return;
    fetch('/cart/coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ coupon: code })
    })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('coupon-msg');
        msg.textContent = data.message;
        msg.style.color = data.success ? 'var(--color-success)' : 'var(--color-danger)';
        if (data.success) {
            baseDiscount = data.discount_value || 0;
            // The discount value itself comes backend calculations, but we simulate re-applying
            location.reload(); // Quickest way to correctly sync real partial discount
        }
    });
}

function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    recalculateTotal();
}

function recalculateTotal() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    let dynamicSubtotal = 0;
    
    checkboxes.forEach(cb => {
        const price = parseFloat(cb.getAttribute('data-price'));
        const qty = parseFloat(cb.getAttribute('data-qty'));
        dynamicSubtotal += price * qty;
    });

    const isFreeship = dynamicSubtotal >= 300000;
    const phiShip = (dynamicSubtotal > 0 && !isFreeship) ? 30000 : 0;
    const totalCalc = Math.max(0, dynamicSubtotal - baseDiscount) + phiShip;

    document.getElementById('selected-count').textContent = checkboxes.length;
    document.getElementById('summary-subtotal').textContent = formatVND(dynamicSubtotal);
    document.getElementById('summary-total').textContent = formatVND(totalCalc);
    
    const countSpan = document.querySelector('#order-summary .summary-row span:first-child');
    if(countSpan) {
        countSpan.innerHTML = `Tạm tính (<span id="item-count">${checkboxes.length}</span> sản phẩm được chọn)`;
    }

    const shipSpan = document.querySelectorAll('#order-summary .summary-row')[1].querySelectorAll('span')[1];
    if(dynamicSubtotal === 0) {
        shipSpan.textContent = '0đ';
    } else {
        shipSpan.textContent = isFreeship ? 'Miễn phí' : '30.000đ';
    }
}

// Initial calculation
document.addEventListener('DOMContentLoaded', recalculateTotal);
</script>
@endpush
@endsection
