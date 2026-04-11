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

                    {{-- Gợi ý mã từ trang chủ --}}
                    @if($suggestedCoupon && $couponCode !== $suggestedCoupon->ma_code)
                    <div style="background: #fff9db; border: 1px dashed #fab005; border-radius: 8px; padding: 10px; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span class="material-icons" style="color:#f08c00; font-size:18px;">local_offer</span>
                        <div style="flex:1;">
                            <div style="font-size:12px; color:#856404; font-weight:600;">Khuyến mãi hot @Trang chủ:</div>
                            <div style="font-size:13px; color:#1a202c;">Tặng bạn mã <strong>{{ $suggestedCoupon->ma_code }}</strong> (giảm {{ $suggestedCoupon->loai === 'percent' ? $suggestedCoupon->gia_tri . '%' : number_format($suggestedCoupon->gia_tri, 0, ',', '.') . 'đ' }})</div>
                        </div>
                        <button type="button" onclick="document.getElementById('coupon-input').value='{{ $suggestedCoupon->ma_code }}'; document.getElementById('btn-apply-coupon').click();"
                            style="background: #fab005; color: white; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            Dùng ngay
                        </button>
                    </div>
                    @endif

                    <div style="display: flex; gap: 8px;">
                        <div style="flex:1; position:relative;">
                            <input type="text" id="coupon-input" value="" placeholder="Nhập mã hoặc chọn từ danh sách..." style="width:100%; height: 40px; border: 1px solid var(--color-border); border-radius: 8px; padding: 0 12px; box-sizing:border-box;" autocomplete="off">
                        </div>
                        <button type="button" id="btn-apply-coupon" class="btn btn-outline" style="height: 40px; padding: 0 var(--space-4); white-space:nowrap;">Áp dụng</button>
                    </div>
                    <div style="margin-top:6px;">
                        <button type="button" id="btn-show-coupons" style="background:none; border:none; color:var(--color-primary); font-size:12px; cursor:pointer; padding:0; text-decoration:underline;">
                            📋 Chọn mã giảm giá →
                        </button>
                    </div>
                    <div id="coupon-message" style="margin-top: 8px; font-size: 12px; display: none;"></div>
                </div>

                {{-- MODAL CHỌỌN MÃ GIẢM GIÁ --}}
                <div id="coupon-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9000; align-items:center; justify-content:center; padding:16px;">
                    <div id="coupon-modal" style="background:#fff; border-radius:20px; width:100%; max-width:480px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 24px 64px rgba(0,0,0,.25);">
                        {{-- Header --}}
                        <div style="padding:20px 24px 14px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                            <div>
                                <div style="font-weight:700; font-size:18px; color:#1a202c;">🎫 Chọn Voucher</div>
                                <div style="font-size:12px; color:#718096; margin-top:2px;">Chọn mã phù hợp nhất với đơn hàng của bạn</div>
                            </div>
                            <button id="btn-close-modal" style="background:none; border:none; cursor:pointer; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; color:#718096; transition:background .15s;" onmouseover="this.style.background='#f7f7f7'" onmouseout="this.style.background='none'">×</button>
                        </div>

                        {{-- Search --}}
                        <div style="padding:14px 24px; border-bottom:1px solid #f0f0f0; flex-shrink:0;">
                            <div style="position:relative;">
                                <span class="material-icons" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#a0aec0; font-size:18px;">search</span>
                                <input id="modal-search" type="text" placeholder="Tìm mã giảm giá..." style="width:100%; height:40px; border:1px solid #e2e8f0; border-radius:10px; padding:0 12px 0 40px; box-sizing:border-box; font-size:13px; outline:none; transition:border-color .2s;" onfocus="this.style.borderColor='var(--color-primary)'" onblur="this.style.borderColor='#e2e8f0'">
                            </div>
                        </div>

                        {{-- List --}}
                        <div id="modal-coupon-list" style="overflow-y:auto; flex:1; padding:12px 16px;">
                            <div id="modal-loading" style="text-align:center; padding:32px; color:#a0aec0;">  
                                <span class="material-icons" style="font-size:40px; display:block; margin-bottom:8px;">hourglass_top</span>
                                Đang tải mã giảm giá...
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div style="padding:16px 24px; border-top:1px solid #f0f0f0; flex-shrink:0; display:flex; gap:12px;">
                            <button id="btn-modal-apply" class="btn btn-primary" style="flex:1; height:48px; font-size:15px; font-weight:700; border-radius:12px; opacity:.5; cursor:not-allowed;" disabled>Áp dụng</button>
                        </div>
                    </div>
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
    <style>
    /* ── Voucher card styles ─────────────────────────────────────────── */
    .voucher-card {
        display: flex;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color .2s, box-shadow .2s, transform .15s;
        background: #fff;
        margin-bottom: 10px;
        position: relative;
    }
    .voucher-card:hover { border-color: var(--color-primary, #e53e3e); box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-1px); }
    .voucher-card.selected { border-color: var(--color-primary, #e53e3e); box-shadow: 0 0 0 3px rgba(229,62,62,.15); }
    .voucher-card.disabled-card { opacity: .6; cursor: not-allowed; }
    .voucher-card.disabled-card:hover { transform: none; box-shadow: none; border-color: #e2e8f0; }
    .voucher-thumbnail {
        width: 80px;
        min-height: 96px;
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
    }
    /* Serrated edge effect */
    .voucher-thumbnail::after {
        content: '';
        position: absolute;
        right: -9px;
        top: 0;
        bottom: 0;
        width: 18px;
        background: radial-gradient(circle at 0 50%, #f7fafc 9px, transparent 10px) center / 18px 18px repeat-y;
    }
    .voucher-body { flex: 1; padding: 12px 14px 10px 22px; min-width: 0; }
    .voucher-title { font-weight: 700; font-size: 15px; color: #1a202c; line-height: 1.3; }
    .voucher-subtitle { font-size: 12px; color: #718096; margin-top: 2px; font-weight: 500; }
    .voucher-condition-extra { font-size: 11.5px; color: #4a5568; margin-top: 3px; }
    .voucher-progress-wrap { margin-top: 7px; background: #edf2f7; border-radius: 99px; height: 4px; overflow: hidden; }
    .voucher-progress-bar { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #feb2b2, #e53e3e); transition: width .4s; }
    .voucher-expire { font-size: 11px; color: #a0aec0; margin-top: 5px; }
    .voucher-expire .urgent { color: #e53e3e; font-weight: 700; }
    .badge-best { position: absolute; top: 0; right: 44px; background: linear-gradient(135deg, #f6ad55, #ed8936); color: white; font-size: 10px; font-weight: 800; padding: 2px 9px 3px; border-radius: 0 0 8px 8px; letter-spacing: .4px; box-shadow: 0 2px 8px rgba(237,137,54,.4); }
    .voucher-check { width: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .voucher-check-circle { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #cbd5e0; display: flex; align-items: center; justify-content: center; transition: all .2s; position: relative; }
    .voucher-card.selected .voucher-check-circle { background: var(--color-primary, #e53e3e); border-color: var(--color-primary, #e53e3e); }
    .voucher-check-icon { display: none; color: white; font-size: 14px !important; }
    .voucher-card.selected .voucher-check-icon { display: flex; }
    .no-coupons-msg { text-align:center; padding:36px 16px; color:#a0aec0; }
    </style>

    <script>
    (function () {
        const COUPON_URL = '{{ route("cart.coupon") }}';
        const AVAIL_URL  = '{{ route("cart.coupons.available") }}';
        const CSRF       = '{{ csrf_token() }}';

        const inputEl       = document.getElementById('coupon-input');
        const applyBtn      = document.getElementById('btn-apply-coupon');
        const messageEl     = document.getElementById('coupon-message');
        const showBtn       = document.getElementById('btn-show-coupons');
        const removeBtn     = document.getElementById('btn-remove-coupon');

        // Modal elements
        const overlay       = document.getElementById('coupon-modal-overlay');
        const closeModalBtn = document.getElementById('btn-close-modal');
        const modalList     = document.getElementById('modal-coupon-list');
        const modalLoading  = document.getElementById('modal-loading');
        const modalApplyBtn = document.getElementById('btn-modal-apply');
        const modalSearch   = document.getElementById('modal-search');

        let couponsCache = null;
        let selectedCode = null;

        // ── Open / close modal ────────────────────────────────────────────────
        function openModal() {
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            if (!couponsCache) fetchCoupons();
            else renderModal(couponsCache);
        }
        function closeModal() {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }

        // ── Fetch ─────────────────────────────────────────────────────────────
        function fetchCoupons() {
            modalLoading.style.display = 'block';
            // Clear old cards
            Array.from(modalList.children).forEach(el => { if (el !== modalLoading) el.remove(); });

            fetch(AVAIL_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    couponsCache = data;
                    renderModal(data);
                })
                .catch(() => {
                    modalLoading.innerHTML = '<span class="material-icons" style="font-size:36px;color:#fc8181;display:block;margin-bottom:6px;">error_outline</span>Không thể tải danh sách mã.';
                });
        }

        // ── Render modal voucher list ──────────────────────────────────────────
        function renderModal(data, filter) {
            modalLoading.style.display = 'none';
            Array.from(modalList.children).forEach(el => { if (el !== modalLoading) el.remove(); });

            let list = filter
                ? data.filter(c => c.ma_code.toUpperCase().includes(filter.toUpperCase()))
                : data;

            if (!list.length) {
                const msg = document.createElement('div');
                msg.className = 'no-coupons-msg';
                msg.innerHTML = '<span class="material-icons" style="font-size:48px;display:block;margin-bottom:8px;">sentiment_dissatisfied</span>Không tìm thấy mã nào.';
                modalList.appendChild(msg);
                return;
            }

            // ── Phân nhóm: "đề xuất tốt nhất" lên đầu → khả dụng → không khả dụng cuối
            const best      = list.filter(c => c.is_best && c.can_apply);
            const applicable = list.filter(c => !c.is_best && c.can_apply);
            const unavailable = list.filter(c => !c.can_apply);

            // Sắp xếp nhóm khả dụng: giảm nhiều nhất lên trên
            applicable.sort((a, b) => b.gia_tri - a.gia_tri);

            const sorted = [...best, ...applicable, ...unavailable];

            // Header "Mã giảm giá" ở đầu nếu có mã khả dụng
            if (best.length + applicable.length > 0) {
                const headerAvail = document.createElement('div');
                headerAvail.style.cssText = 'margin: 4px 0 10px; display: flex; align-items: center; gap: 10px;';
                headerAvail.innerHTML = `
                    <div style="flex:1; height:1px; background:#e2e8f0;"></div>
                    <span style="font-size:12px; font-weight:800; color:#4a5568; white-space:nowrap; letter-spacing:.3px;">
                        Mã giảm giá
                    </span>
                    <div style="flex:1; height:1px; background:#e2e8f0;"></div>`;
                modalList.appendChild(headerAvail);
            }

            let unavailableSectionAdded = false;

            sorted.forEach(c => {
                // Chèn header "Voucher không khả dụng" trước card đầu tiên của nhóm này
                if (!c.can_apply && !unavailableSectionAdded) {
                    unavailableSectionAdded = true;
                    const sep = document.createElement('div');
                    sep.style.cssText = 'margin: 14px 0 8px; display: flex; align-items: center; gap: 10px;';
                    sep.innerHTML = `
                        <div style="flex:1; height:1px; background:#e2e8f0;"></div>
                        <span style="font-size:12px; font-weight:800; color:#a0aec0; white-space:nowrap; letter-spacing:.3px;">
                            Voucher không khả dụng
                        </span>
                        <div style="flex:1; height:1px; background:#e2e8f0;"></div>`;
                    modalList.appendChild(sep);
                }

                const card = document.createElement('div');
                card.className = 'voucher-card' + (selectedCode === c.ma_code ? ' selected' : '');
                card.dataset.code = c.ma_code;

                // Thumbnail — mờ đi nếu không khả dụng
                const thumbStyle = c.can_apply
                    ? 'background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);'
                    : 'background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);';

                const discText = c.loai === 'percent'
                    ? `<span style="font-size:22px;font-weight:900;color:white;line-height:1;">${c.gia_tri}%</span><span style="font-size:9px;color:rgba(255,255,255,.8);margin-top:2px;letter-spacing:.5px;">GIẢM</span>`
                    : `<span style="font-size:12px;font-weight:800;color:white;line-height:1.2;text-align:center;padding:0 4px;">${c.label.replace('Giảm ', '')}</span><span style="font-size:9px;color:rgba(255,255,255,.8);margin-top:2px;">GIẢM GIÁ</span>`;

                // Expiry badge
                let expHtml = '';
                if (c.time_left) {
                    expHtml = `<span class="urgent">${c.time_left}</span>`;
                } else if (c.het_han === 'Vĩnh viễn') {
                    expHtml = `<span>Không giới hạn</span>`;
                } else {
                    expHtml = `<span>HSD: ${c.het_han}</span>`;
                }

                // Progress bar
                const progressHtml = c.so_luong
                    ? `<div class="voucher-progress-wrap"><div class="voucher-progress-bar" style="width:${c.used_percent}%;"></div></div>
                       <div style="font-size:10px;color:#a0aec0;margin-top:2px;">Đã dùng ${c.used_percent}% lượt</div>`
                    : '';

                // Can't apply notice
                const cantApplyHtml = (!c.can_apply && c.don_hang_toi_thieu_fmt)
                    ? `<div style="font-size:11px;color:#a0aec0;margin-top:4px;font-weight:600;">Cần đặt tối thiểu ${c.don_hang_toi_thieu_fmt}</div>`
                    : (!c.can_apply ? `<div style="font-size:11px;color:#a0aec0;margin-top:4px;">Không đáp ứng điều kiện</div>` : '');

                // Extra conditions (beyond min-order)
                const extraConds = c.conditions.filter((_, i) => i > 0);

                card.innerHTML = `
                    ${c.is_best ? '<div class="badge-best">⭐ Đề xuất tốt nhất</div>' : ''}
                    <div class="voucher-thumbnail" style="${thumbStyle} width:80px; min-height:96px; display:flex; flex-direction:column; align-items:center; justify-content:center; flex-shrink:0; position:relative;">
                        ${discText}
                    </div>
                    <div class="voucher-body">
                        <div class="voucher-title" style="${!c.can_apply ? 'color:#a0aec0;' : ''}">${c.label}</div>
                        <div class="voucher-subtitle">${c.don_hang_toi_thieu_fmt ? 'Đơn tối thiểu ' + c.don_hang_toi_thieu_fmt : 'Không yêu cầu đơn tối thiểu'}</div>
                        ${extraConds.length ? `<div class="voucher-condition-extra">${extraConds.join(' • ')}</div>` : ''}
                        ${progressHtml}
                        ${cantApplyHtml}
                        <div class="voucher-expire">${expHtml}</div>
                    </div>
                    <div class="voucher-check">
                        <div class="voucher-check-circle" style="${!c.can_apply ? 'border-color:#e2e8f0; background:#f7fafc;' : ''}">
                            <span class="material-icons voucher-check-icon">check</span>
                        </div>
                    </div>`;

                if (!c.can_apply) {
                    card.classList.add('disabled-card');
                } else {
                    card.addEventListener('click', () => selectCard(c.ma_code, card));
                }

                modalList.appendChild(card);
            });
        }

        function selectCard(code, clickedCard) {
            document.querySelectorAll('#modal-coupon-list .voucher-card').forEach(el => el.classList.remove('selected'));
            if (selectedCode === code) {
                selectedCode = null;
                modalApplyBtn.disabled = true;
                modalApplyBtn.style.opacity = '.5';
                modalApplyBtn.style.cursor = 'not-allowed';
            } else {
                selectedCode = code;
                clickedCard.classList.add('selected');
                modalApplyBtn.disabled = false;
                modalApplyBtn.style.opacity = '1';
                modalApplyBtn.style.cursor = 'pointer';
            }
        }

        // ── Modal search ───────────────────────────────────────────────────────
        if (modalSearch) {
            modalSearch.addEventListener('input', () => {
                if (couponsCache) renderModal(couponsCache, modalSearch.value.trim());
            });
        }

        // ── Wire up open/close ─────────────────────────────────────────────────
        if (showBtn)       showBtn.addEventListener('click', openModal);
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
        if (overlay)       overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // ── Apply from modal ───────────────────────────────────────────────────
        if (modalApplyBtn) {
            modalApplyBtn.addEventListener('click', () => {
                if (!selectedCode) return;
                if (inputEl) inputEl.value = selectedCode;
                closeModal();
                applyBtn && applyBtn.click();
            });
        }

        // ── Apply coupon (manual input) ────────────────────────────────────────
        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                const coupon = inputEl ? inputEl.value.trim() : '';
                if (!coupon) { showMessage('Vui lòng nhập mã giảm giá.', false); return; }

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

        // ── Remove coupon ──────────────────────────────────────────────────────
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
