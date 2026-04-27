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
                <p style="color: var(--color-text-muted); margin-bottom: var(--space-8);">Hãy khám phá hàng nghìn tựa sách hấp dẫn tại Bookverse.</p>
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

                    @if($suggestedCoupon && $couponCode !== $suggestedCoupon->ma_code)
                    <div style="background: #fff9db; border: 1px dashed #fab005; border-radius: 8px; padding: 10px; margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                        <span class="material-icons" style="color:#f08c00; font-size:18px;">local_offer</span>
                        <div style="flex:1;">
                            <div style="font-size:12px; color:#856404; font-weight:600;">Khuyến mãi hot @Trang chủ:</div>
                            <div style="font-size:13px; color:#1a202c;">Tặng mã <strong>{{ $suggestedCoupon->ma_code }}</strong> (giảm {{ $suggestedCoupon->loai === 'percent' ? $suggestedCoupon->gia_tri . '%' : number_format($suggestedCoupon->gia_tri, 0, ',', '.') . 'đ' }})</div>
                            @if(isset($missingAmount) && $missingAmount > 0)
                                <div style="font-size: 12px; color: #d32f2f; margin-top: 4px; font-weight: 500;">
                                    <span class="material-icons" style="font-size: 13px; vertical-align: middle;">info</span>
                                    Mua thêm <strong>{{ number_format($missingAmount, 0, ',', '.') }}đ</strong> để áp dụng mã này!
                                </div>
                            @endif
                        </div>
                        @if(!isset($missingAmount) || $missingAmount <= 0)
                        <button type="button" onclick="document.getElementById('coupon-input').value='{{ $suggestedCoupon->ma_code }}'; document.getElementById('btn-apply-coupon').click();" 
                            style="background: #fab005; color: white; border: none; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer;">
                            Dùng ngay
                        </button>
                        @else
                        <a href="{{ url('/products') }}" style="background: #e9ecef; color: #495057; border: 1px solid #ced4da; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: none;">
                            Mua thêm
                        </a>
                        @endif
                    </div>
                    @endif
                    {{-- Manual input: still available for users who paste a code directly. --}}
                    <div style="display: flex; gap: 8px;">
                        <div style="flex:1; position:relative;">
                            <input type="text" id="coupon-input" value="" placeholder="Nhập mã hoặc chọn từ danh sách..." style="width:100%; height: 40px; border: 1px solid var(--color-border); border-radius: 8px; padding: 0 12px; box-sizing:border-box;" autocomplete="off">
                        </div>
                        <button type="button" id="btn-apply-coupon" class="btn btn-outline" style="height: 40px; padding: 0 var(--space-4); white-space:nowrap;">Áp dụng</button>
                    </div>

                    {{-- Prominent voucher selector (Shopee/Lazada-style). Clicking this opens the modal. --}}
                    <button type="button" id="btn-show-coupons" class="voucher-trigger" style="margin-top:10px;">
                        <span class="voucher-trigger__icon material-icons">local_activity</span>
                        <span class="voucher-trigger__main">
                            <span class="voucher-trigger__title">Chọn mã giảm giá</span>
                            <span class="voucher-trigger__sub" id="voucher-trigger-sub">Xem danh sách mã khả dụng cho đơn hàng này</span>
                        </span>
                        <span class="voucher-trigger__count" id="voucher-trigger-count" hidden>0</span>
                        <span class="voucher-trigger__chevron material-icons">chevron_right</span>
                    </button>

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
    document.addEventListener('DOMContentLoaded', function () {
        const COUPON_URL   = '{{ route("cart.coupon") }}';
        const AVAIL_URL    = '{{ route("cart.coupons.available") }}';
        const CSRF         = '{{ csrf_token() }}';

        const inputEl      = document.getElementById('coupon-input');
        const applyBtn     = document.getElementById('btn-apply-coupon');
        const messageEl    = document.getElementById('coupon-message');
        const showBtn      = document.getElementById('btn-show-coupons');
        const removeBtn    = document.getElementById('btn-remove-coupon');
        const triggerSubEl = document.getElementById('voucher-trigger-sub');
        const triggerCntEl = document.getElementById('voucher-trigger-count');

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

        // ═════════════════════════════════════════════════════════════
        //  VOUCHER MODAL — chọn mã giảm giá (card list style)
        // ═════════════════════════════════════════════════════════════
        const modalEl        = document.getElementById('voucher-modal');
        const modalCloseBtn  = document.getElementById('voucher-modal-close');
        const modalApplyBtn  = document.getElementById('voucher-modal-apply');
        const modalSearchEl  = document.getElementById('voucher-modal-search');
        const modalListApp   = document.getElementById('voucher-list-applicable');
        const modalListNon   = document.getElementById('voucher-list-non-applicable');
        const modalSecApp    = document.getElementById('voucher-section-applicable');
        const modalSecNon    = document.getElementById('voucher-section-non-applicable');
        const modalLoadingEl = document.getElementById('voucher-modal-loading');

        let voucherData   = { applicable: [], non_applicable: [] };
        let selectedCode  = null;

        function openVoucherModal() {
            if (!modalEl) return;
            modalEl.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            // Nếu mã đang áp dụng → pre-select
            selectedCode = @json($couponCode ?? null);
            fetchVouchers();
        }
        function closeVoucherModal() {
            if (!modalEl) return;
            modalEl.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        function fetchVouchers() {
            modalLoadingEl.style.display = 'block';
            modalSecApp.style.display    = 'none';
            modalSecNon.style.display    = 'none';
            fetch(AVAIL_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    voucherData = {
                        applicable:     Array.isArray(data) ? data : (data.applicable || []),
                        non_applicable: Array.isArray(data) ? []   : (data.non_applicable || []),
                    };
                    updateVoucherTrigger();
                    renderVoucherModal();
                })
                .catch(() => {
                    modalLoadingEl.textContent = 'Không thể tải danh sách mã.';
                });
        }

        // Update the Shopee-style trigger summary (count + best offer teaser).
        function updateVoucherTrigger() {
            const appCount = voucherData.applicable.length;
            if (triggerCntEl) {
                if (appCount > 0) {
                    triggerCntEl.hidden = false;
                    triggerCntEl.textContent = appCount + ' ưu đãi';
                } else {
                    triggerCntEl.hidden = true;
                }
            }
            if (triggerSubEl) {
                if (appCount === 0) {
                    triggerSubEl.textContent = 'Hiện chưa có mã nào khả dụng cho đơn hàng này';
                } else {
                    const best = voucherData.applicable[0];
                    triggerSubEl.textContent = best
                        ? 'Ưu đãi tốt nhất: ' + (best.full_label || best.big_label || best.ma_code)
                        : 'Có ' + appCount + ' mã khả dụng — nhấn để chọn';
                }
            }
        }

        // Silent refresh (no modal) — called on page load + after apply/remove.
        function preloadVoucherTrigger() {
            fetch(AVAIL_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    voucherData = {
                        applicable:     Array.isArray(data) ? data : (data.applicable || []),
                        non_applicable: Array.isArray(data) ? []   : (data.non_applicable || []),
                    };
                    updateVoucherTrigger();
                })
                .catch(() => { /* silent */ });
        }

        function renderVoucherModal() {
            modalLoadingEl.style.display = 'none';

            const search = (modalSearchEl.value || '').trim().toUpperCase();
            const filterFn = c => !search || c.ma_code.includes(search);
            const appList  = voucherData.applicable.filter(filterFn);
            const nonList  = voucherData.non_applicable.filter(filterFn);

            // Applicable section
            if (appList.length) {
                modalSecApp.style.display = 'block';
                modalListApp.innerHTML    = appList.map(c => voucherCardHtml(c, false)).join('');
            } else {
                modalSecApp.style.display = 'block';
                modalListApp.innerHTML    = '<div class="voucher-empty">Không có mã nào khả dụng.</div>';
            }

            // Non-applicable section
            if (nonList.length) {
                modalSecNon.style.display = 'block';
                modalListNon.innerHTML    = nonList.map(c => voucherCardHtml(c, true)).join('');
            } else {
                modalSecNon.style.display = 'none';
            }

            // Bind click
            modalEl.querySelectorAll('.voucher-card').forEach(card => {
                card.addEventListener('click', () => {
                    if (card.classList.contains('is-disabled')) return;
                    selectedCode = card.dataset.code;
                    modalEl.querySelectorAll('.voucher-card').forEach(x => x.classList.remove('is-selected'));
                    card.classList.add('is-selected');
                    modalApplyBtn.disabled = false;
                });
            });

            // Nếu có pre-selected → đánh dấu
            if (selectedCode) {
                const target = modalEl.querySelector(`.voucher-card[data-code="${selectedCode}"]:not(.is-disabled)`);
                if (target) {
                    target.classList.add('is-selected');
                    modalApplyBtn.disabled = false;
                }
            }
            modalApplyBtn.disabled = !selectedCode;
        }

        function voucherCardHtml(c, disabled) {
            const badge = c.is_best && !disabled
                ? '<span class="voucher-best-badge">Đề xuất tốt nhất</span>' : '';
            const condsHtml = (c.conditions || []).map(t => `<div class="voucher-cond">• ${escapeHtml(t)}</div>`).join('');
            const reasonHtml = disabled && c.reason
                ? `<div class="voucher-reason">${escapeHtml(c.reason)}</div>` : '';
            const usageHtml = c.usage_pct > 0
                ? `<div class="voucher-usage"><div class="voucher-usage-bar" style="width:${c.usage_pct}%"></div></div>
                   <div class="voucher-usage-text">Đã dùng ${c.usage_pct}% lượt</div>`
                : `<div class="voucher-usage-text" style="margin-top:4px">Đã dùng 0% lượt</div>`;

            return `
                <div class="voucher-card ${disabled ? 'is-disabled' : ''}" data-code="${escapeHtml(c.ma_code)}">
                    <div class="voucher-left">
                        <div class="voucher-big">${escapeHtml(c.big_label)}</div>
                        <div class="voucher-small">GIẢM GIÁ</div>
                    </div>
                    <div class="voucher-body">
                        ${badge}
                        <div class="voucher-title">${escapeHtml(c.full_label)}</div>
                        <div class="voucher-code">Mã: <strong>${escapeHtml(c.ma_code)}</strong></div>
                        <div class="voucher-conds">${condsHtml}</div>
                        ${usageHtml}
                        <div class="voucher-expiry">${escapeHtml(c.het_han_text || '')}</div>
                        ${reasonHtml}
                    </div>
                    <div class="voucher-radio" aria-hidden="true"></div>
                </div>`;
        }

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, m =>
                ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
        }

        // Open modal when clicking the prominent "Chọn mã giảm giá" trigger.
        if (showBtn) {
            showBtn.addEventListener('click', function (e) {
                e.preventDefault();
                openVoucherModal();
            });
        }

        // Per the UX spec: focusing the input field should ALSO open the picker.
        // We still allow manual typing — clicking the input first opens the modal
        // once; users can then close the modal and type. We avoid focus-loop by
        // only opening while modal is closed AND we haven't just returned from it.
        if (inputEl) {
            let justClosedAt = 0;
            inputEl.addEventListener('focus', function () {
                if (!modalEl) return;
                if (modalEl.classList.contains('is-open')) return;
                if (Date.now() - justClosedAt < 400) return; // debounce after close
                openVoucherModal();
                inputEl.blur(); // surface the modal instead of keyboard
            });
            // Track when modal closes so we don't immediately re-open on blur/focus cycle.
            if (modalEl) {
                const observer = new MutationObserver(() => {
                    if (!modalEl.classList.contains('is-open')) justClosedAt = Date.now();
                });
                observer.observe(modalEl, { attributes: true, attributeFilter: ['class'] });
            }
            // Allow Enter in the input to apply.
            inputEl.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); applyBtn && applyBtn.click(); }
            });
        }

        if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeVoucherModal);
        if (modalEl) {
            modalEl.addEventListener('click', e => {
                if (e.target === modalEl) closeVoucherModal();
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && modalEl.classList.contains('is-open')) closeVoucherModal();
            });
        }
        if (modalSearchEl) {
            modalSearchEl.addEventListener('input', renderVoucherModal);
        }
        if (modalApplyBtn) {
            modalApplyBtn.addEventListener('click', function () {
                if (!selectedCode) return;
                if (inputEl) inputEl.value = selectedCode;
                closeVoucherModal();
                if (applyBtn) applyBtn.click();
            });
        }

        // Preload the count / best-offer teaser on the trigger once the page is ready.
        preloadVoucherTrigger();
    });
    </script>

    {{-- ═══════════════════════════════════════════════════════════════
         VOUCHER MODAL HTML
         ═══════════════════════════════════════════════════════════════ --}}
    <div id="voucher-modal" class="voucher-modal" role="dialog" aria-modal="true" aria-labelledby="voucher-modal-title">
        <div class="voucher-modal__panel" role="document">
            <div class="voucher-modal__header">
                <div>
                    <h2 id="voucher-modal-title" class="voucher-modal__title">
                        <span class="material-icons" style="color:#dc2626">local_activity</span>
                        Chọn Voucher
                    </h2>
                    <p class="voucher-modal__subtitle">Chọn mã phù hợp nhất với đơn hàng của bạn</p>
                </div>
                <button type="button" id="voucher-modal-close" class="voucher-modal__close" aria-label="Đóng">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="voucher-modal__search">
                <span class="material-icons">search</span>
                <input type="text" id="voucher-modal-search" placeholder="Tìm mã giảm giá..." autocomplete="off">
            </div>

            <div class="voucher-modal__body">
                <div id="voucher-modal-loading" class="voucher-loading">Đang tải danh sách mã giảm giá...</div>

                <section id="voucher-section-applicable" class="voucher-section" style="display:none">
                    <h3 class="voucher-section__title">
                        <span class="voucher-section__dot" style="background:#16a34a"></span>
                        Mã có thể dùng
                    </h3>
                    <div id="voucher-list-applicable" class="voucher-list"></div>
                </section>

                <section id="voucher-section-non-applicable" class="voucher-section" style="display:none">
                    <h3 class="voucher-section__title">
                        <span class="voucher-section__dot" style="background:#9ca3af"></span>
                        Mã không thể áp dụng
                    </h3>
                    <div id="voucher-list-non-applicable" class="voucher-list"></div>
                </section>
            </div>

            <div class="voucher-modal__footer">
                <button type="button" id="voucher-modal-apply" class="voucher-modal__apply-btn" disabled>
                    Áp dụng
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         VOUCHER MODAL STYLES
         ═══════════════════════════════════════════════════════════════ --}}
    <style>
    /* ═══════════════════════════════════════════════════════════════
       VOUCHER TRIGGER (the "Chọn mã giảm giá" button in order summary)
       ═══════════════════════════════════════════════════════════════ */
    .voucher-trigger {
        width: 100%;
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        background: linear-gradient(90deg, #fff7ed 0%, #ffedd5 100%);
        border: 1.5px dashed #f97316;
        border-radius: 10px;
        cursor: pointer;
        text-align: left;
        transition: transform .12s ease, box-shadow .18s ease, border-color .15s;
        position: relative;
    }
    .voucher-trigger:hover {
        border-color: #ea580c;
        box-shadow: 0 6px 16px rgba(249, 115, 22, .18);
        transform: translateY(-1px);
    }
    .voucher-trigger:active { transform: translateY(0); }
    .voucher-trigger__icon {
        flex-shrink: 0;
        color: #ea580c;
        font-size: 26px;
        background: #fff;
        border-radius: 50%;
        padding: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
    }
    .voucher-trigger__main {
        flex: 1; min-width: 0;
        display: flex; flex-direction: column; gap: 2px;
    }
    .voucher-trigger__title {
        font-size: 14px; font-weight: 700; color: #9a3412;
    }
    .voucher-trigger__sub {
        font-size: 12px; color: #b45309;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .voucher-trigger__count {
        flex-shrink: 0;
        background: #ef4444;
        color: #fff;
        font-size: 11px; font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        letter-spacing: .2px;
    }
    .voucher-trigger__chevron {
        flex-shrink: 0;
        color: #ea580c;
        font-size: 22px;
        transition: transform .15s ease;
    }
    .voucher-trigger:hover .voucher-trigger__chevron { transform: translateX(3px); }

    .voucher-modal {
        position: fixed; inset: 0;
        background: rgba(15, 23, 42, .55);
        display: none;
        align-items: center; justify-content: center;
        z-index: 10000;
        padding: 20px;
        animation: voucherFadeIn .2s ease;
    }
    .voucher-modal.is-open { display: flex; }
    @keyframes voucherFadeIn {
        from { opacity: 0; } to { opacity: 1; }
    }
    .voucher-modal__panel {
        background: #fff;
        width: min(520px, 100%);
        max-height: 90vh;
        border-radius: 14px;
        display: flex; flex-direction: column;
        box-shadow: 0 25px 60px rgba(0,0,0,.25);
        overflow: hidden;
        animation: voucherSlideUp .25s ease;
    }
    @keyframes voucherSlideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    /* Header */
    .voucher-modal__header {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 18px 20px 12px;
        border-bottom: 1px solid #f0f0f2;
    }
    .voucher-modal__title {
        margin: 0;
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 1.15rem; font-weight: 800; color: #111827;
    }
    .voucher-modal__subtitle {
        margin: 4px 0 0; font-size: .82rem; color: #6b7280;
    }
    .voucher-modal__close {
        background: none; border: none; cursor: pointer;
        padding: 4px; border-radius: 6px; color: #6b7280;
    }
    .voucher-modal__close:hover { background: #f3f4f6; color: #111827; }

    /* Search */
    .voucher-modal__search {
        position: relative;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f2;
    }
    .voucher-modal__search .material-icons {
        position: absolute;
        left: 30px; top: 50%;
        transform: translateY(-50%);
        color: #9ca3af; font-size: 20px;
    }
    .voucher-modal__search input {
        width: 100%;
        padding: 9px 12px 9px 38px;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        font-size: .88rem;
        background: #f9fafb;
        outline: none;
        transition: border-color .15s, background .15s;
    }
    .voucher-modal__search input:focus {
        border-color: #dc2626; background: #fff;
    }

    /* Body */
    .voucher-modal__body {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 10px 20px 20px;
    }
    .voucher-loading, .voucher-empty {
        padding: 30px 0;
        text-align: center;
        color: #9ca3af;
        font-size: .9rem;
    }
    .voucher-section { margin-top: 14px; }
    .voucher-section__title {
        margin: 0 0 10px;
        display: inline-flex; align-items: center; gap: 8px;
        font-size: .8rem; font-weight: 700;
        color: #374151;
        text-transform: uppercase; letter-spacing: .5px;
    }
    .voucher-section__dot {
        width: 8px; height: 8px; border-radius: 50%;
    }
    .voucher-list { display: flex; flex-direction: column; gap: 12px; }

    /* Voucher card */
    .voucher-card {
        display: flex; align-items: stretch;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s, transform .1s;
        position: relative;
    }
    .voucher-card:hover:not(.is-disabled) {
        border-color: #dc2626;
        box-shadow: 0 6px 18px rgba(220,38,38,.12);
    }
    .voucher-card.is-selected {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220,38,38,.15);
    }
    .voucher-card.is-disabled {
        cursor: not-allowed;
        opacity: .62;
        filter: grayscale(.35);
    }
    .voucher-left {
        flex: 0 0 96px;
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: #fff;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 12px 6px;
        position: relative;
    }
    .voucher-left::after {
        /* zig-zag divider dotted line */
        content: "";
        position: absolute;
        right: -6px; top: 0; bottom: 0;
        width: 12px;
        background:
            radial-gradient(circle at 6px 6px, #fff 5px, transparent 5.5px) 0 0/12px 12px repeat-y;
    }
    .voucher-big {
        font-size: 1.35rem; font-weight: 800;
        line-height: 1.1; letter-spacing: .5px;
        text-align: center;
    }
    .voucher-small {
        font-size: .62rem; font-weight: 700;
        letter-spacing: 1.5px;
        margin-top: 3px;
        opacity: .95;
    }
    .voucher-body {
        flex: 1;
        padding: 12px 40px 12px 18px;
        display: flex; flex-direction: column;
        gap: 3px;
        min-width: 0;
    }
    .voucher-title {
        font-size: .95rem; font-weight: 700; color: #111827;
    }
    .voucher-code {
        font-size: .78rem; color: #6b7280;
    }
    .voucher-code strong {
        color: #dc2626;
        letter-spacing: .5px;
    }
    .voucher-conds {
        margin-top: 3px;
    }
    .voucher-cond {
        font-size: .76rem; color: #6b7280; line-height: 1.4;
    }
    .voucher-usage {
        margin-top: 6px;
        height: 4px;
        background: #f3f4f6;
        border-radius: 999px;
        overflow: hidden;
    }
    .voucher-usage-bar {
        height: 100%;
        background: linear-gradient(90deg, #fca5a5, #dc2626);
        border-radius: 999px;
        transition: width .3s;
    }
    .voucher-usage-text {
        font-size: .72rem; color: #9ca3af;
        margin-top: 2px;
    }
    .voucher-expiry {
        margin-top: 4px;
        font-size: .74rem;
        color: #9ca3af;
        font-weight: 500;
    }
    .voucher-reason {
        margin-top: 6px;
        padding: 4px 8px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: .72rem; font-weight: 600;
        border-radius: 6px;
        display: inline-block;
        align-self: flex-start;
    }
    .voucher-best-badge {
        position: absolute;
        top: 10px; right: 42px;
        background: linear-gradient(90deg, #f59e0b, #d97706);
        color: #fff;
        padding: 3px 9px;
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .3px;
        border-radius: 999px;
        text-transform: uppercase;
        box-shadow: 0 2px 4px rgba(0,0,0,.12);
    }
    .voucher-radio {
        position: absolute;
        right: 14px; top: 50%;
        transform: translateY(-50%);
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        background: #fff;
        transition: border-color .15s, background .15s;
        flex-shrink: 0;
    }
    .voucher-card.is-selected .voucher-radio {
        border-color: #dc2626;
        background: #dc2626;
        box-shadow: inset 0 0 0 3px #fff;
    }

    /* Footer */
    .voucher-modal__footer {
        padding: 14px 20px;
        border-top: 1px solid #f0f0f2;
        background: #fff;
    }
    .voucher-modal__apply-btn {
        width: 100%;
        padding: 12px 20px;
        background: linear-gradient(90deg, #16a34a, #15803d);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: .95rem; font-weight: 700;
        cursor: pointer;
        transition: opacity .15s, transform .1s, box-shadow .15s;
    }
    .voucher-modal__apply-btn:not(:disabled):hover {
        box-shadow: 0 6px 16px rgba(22,163,74,.3);
    }
    .voucher-modal__apply-btn:not(:disabled):active { transform: scale(.98); }
    .voucher-modal__apply-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        opacity: .75;
    }

    /* Mobile */
    @media (max-width: 520px) {
        .voucher-modal { padding: 0; }
        .voucher-modal__panel {
            width: 100%; height: 100vh; max-height: 100vh;
            border-radius: 0;
        }
    }
    </style>
@endsection
