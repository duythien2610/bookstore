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
                <div style="background: var(--color-bg-alt); padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); position: relative;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Mã giảm giá</label>
                    @if($couponCode)
                    <div style="display:flex; align-items:center; gap:8px; background:#d4edda; border:1px solid #28a745; border-radius:8px; padding:8px 12px; margin-bottom:8px;">
                        <span class="material-icons" style="color:#28a745; font-size:18px;">check_circle</span>
                        <span style="color:#155724; font-size:13px; font-weight:600;">Đã áp dụng mã: <strong>{{ $couponCode }}</strong></span>
                        <button type="button" id="btn-remove-coupon" style="margin-left:auto; background:none; border:none; cursor:pointer; color:#721c24; font-size:18px;" title="Xóa mã">✕</button>
                    </div>
                    @endif
                    <div style="display: flex; gap: 8px;">
                        <div style="flex:1; position:relative;">
                            <input type="text" id="coupon-input" value="" placeholder="Nhập mã hoặc chọn từ danh sách..." style="width:100%; height: 40px; border: 1px solid var(--color-border); border-radius: 8px; padding: 0 12px; box-sizing:border-box;" autocomplete="off">
                            {{-- Dropdown danh sách mã --}}
                            <div id="coupon-dropdown" style="display:none; position:absolute; top:44px; left:0; right:0; background:white; border:1px solid var(--color-border); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); z-index:100; max-height:220px; overflow-y:auto;">
                                <div id="coupon-loading" style="padding:12px; text-align:center; color:var(--color-text-muted); font-size:13px;">Đang tải...</div>
                                <div id="coupon-list"></div>
                            </div>
                        </div>
                        <button type="button" id="btn-apply-coupon" class="btn btn-outline" style="height: 40px; padding: 0 var(--space-4); white-space:nowrap;">Áp dụng</button>
                    </div>
                    <div style="margin-top:6px;">
                        <button type="button" id="btn-show-coupons" style="background:none; border:none; color:var(--color-primary); font-size:12px; cursor:pointer; padding:0; text-decoration:underline;">
                            📋 Xem mã khuyến mãi có thể dùng
                        </button>
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
    (function () {
        const COUPON_URL   = '{{ route("cart.coupon") }}';
        const AVAIL_URL    = '{{ route("cart.coupons.available") }}';
        const CSRF         = '{{ csrf_token() }}';

        const inputEl      = document.getElementById('coupon-input');
        const applyBtn     = document.getElementById('btn-apply-coupon');
        const messageEl    = document.getElementById('coupon-message');
        const dropdownEl   = document.getElementById('coupon-dropdown');
        const couponListEl = document.getElementById('coupon-list');
        const loadingEl    = document.getElementById('coupon-loading');
        const showBtn      = document.getElementById('btn-show-coupons');
        const removeBtn    = document.getElementById('btn-remove-coupon');

        let couponsCache = null;

        // ── Fetch & render danh sách mã ────────────────────────────────────
        function loadAndShowDropdown() {
            dropdownEl.style.display = 'block';
            if (couponsCache !== null) { renderCoupons(couponsCache); return; }

            loadingEl.style.display  = 'block';
            couponListEl.innerHTML   = '';

            fetch(AVAIL_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    couponsCache = data;
                    loadingEl.style.display = 'none';
                    renderCoupons(data);
                })
                .catch(() => {
                    loadingEl.textContent = 'Không thể tải danh sách mã.';
                });
        }

        function renderCoupons(data) {
            loadingEl.style.display = 'none';
            couponListEl.innerHTML  = '';
            if (!data.length) {
                couponListEl.innerHTML = '<div style="padding:12px; text-align:center; color:var(--color-text-muted); font-size:13px;">Không có mã nào khả dụng.</div>';
                return;
            }
            data.forEach(c => {
                const row = document.createElement('div');
                row.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding:10px 14px; cursor:pointer; border-bottom:1px solid #f0f0f0; transition:background .15s;';
                row.innerHTML = `
                    <div>
                        <span style="font-weight:700; color:var(--color-primary); font-size:14px;">${c.ma_code}</span>
                        <span style="margin-left:8px; font-size:12px; color:var(--color-text-muted);">HSD: ${c.het_han}</span>
                    </div>
                    <span style="font-size:13px; font-weight:600; color:var(--color-danger);">${c.label}</span>`;
                row.addEventListener('mouseenter', () => row.style.background = '#f5f5f5');
                row.addEventListener('mouseleave', () => row.style.background = '');
                row.addEventListener('click', () => {
                    inputEl.value = c.ma_code;
                    dropdownEl.style.display = 'none';
                });
                couponListEl.appendChild(row);
            });
        }

        // ── Hiển thị dropdown khi focus hoặc click nút xem ─────────────────
        if (inputEl) {
            inputEl.addEventListener('focus', loadAndShowDropdown);
            inputEl.addEventListener('input', () => {
                if (inputEl.value.trim()) {
                    const filtered = (couponsCache || []).filter(c => c.ma_code.includes(inputEl.value.toUpperCase()));
                    renderCoupons(filtered);
                    dropdownEl.style.display = 'block';
                }
            });
        }
        if (showBtn) {
            showBtn.addEventListener('click', loadAndShowDropdown);
        }

        // ── Đóng dropdown khi click ngoài ───────────────────────────────────
        document.addEventListener('click', e => {
            if (dropdownEl && !dropdownEl.contains(e.target) && e.target !== inputEl && e.target !== showBtn) {
                dropdownEl.style.display = 'none';
            }
        });

        // ── Apply coupon ────────────────────────────────────────────────────
        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                const coupon = inputEl ? inputEl.value.trim() : '';
                if (!coupon) {
                    showMessage('Vui lòng nhập mã giảm giá.', false);
                    return;
                }

                applyBtn.disabled = true;
                applyBtn.textContent = 'Đang áp dụng...';

                fetch(COUPON_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ coupon })
                })
                .then(r => r.json())
                .then(data => {
                    applyBtn.disabled = false;
                    applyBtn.textContent = 'Áp dụng';
                    showMessage(data.message, data.success);
                    if (data.success) {
                        document.getElementById('discount-row').style.display = 'flex';
                        document.getElementById('discount-amount').textContent = '-' + new Intl.NumberFormat('vi-VN').format(data.discount) + 'đ';
                        document.getElementById('final-total').textContent = new Intl.NumberFormat('vi-VN').format(data.final_total) + 'đ';
                        // Reload trang để cập nhật trạng thái mã đã áp dụng
                        setTimeout(() => location.reload(), 700);
                    }
                })
                .catch(() => {
                    applyBtn.disabled = false;
                    applyBtn.textContent = 'Áp dụng';
                    showMessage('Lỗi kết nối, vui lòng thử lại.', false);
                });
            });
        }

        // ── Remove coupon ───────────────────────────────────────────────────
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                fetch(COUPON_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ coupon: '', remove: true })
                })
                .then(() => location.reload())
                .catch(() => location.reload());
            });
        }

        function showMessage(msg, success) {
            if (!messageEl) return;
            messageEl.style.display = 'block';
            messageEl.textContent   = msg;
            messageEl.style.color   = success ? 'var(--color-success)' : 'var(--color-danger)';
        }
    })();
    </script>
@endsection
