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
        <div class="cart-grid" style="margin-top: var(--space-8);">
            {{-- Checkout Form --}}
            <div>
                @if ($errors->any())
                    <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(session('error'))
                    <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ uniqid('order_', true) }}">
                    <input type="text" name="website_url" value="" style="display:none;" tabindex="-1" autocomplete="off">
                    {{-- Shipping Info --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
                        <h2 style="margin-bottom: var(--space-8); font-size: 28px;">Thông tin giao hàng</h2>



                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6); margin-bottom: var(--space-6);">
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Họ và tên</label>
                                <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" value="{{ $defaultAddress->ho_ten ?? $user->ho_ten ?? '' }}" required style="height: 52px; border-radius: 12px;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Số điện thoại</label>
                                <input type="tel" name="so_dien_thoai" id="checkout_phone" class="form-control" placeholder="0912 345 678" value="{{ $defaultAddress->so_dien_thoai ?? $user->so_dien_thoai ?? '' }}" required style="height: 52px; border-radius: 12px;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: var(--space-6);">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Địa chỉ <span style="color: var(--color-text-muted); font-size: 12px;">(tùy chọn)</span></label>
                            <input type="text" name="dia_chi" id="input-dia-chi" class="form-control" placeholder="Số nhà, đường..." value="{{ $defaultAddress->dia_chi ?? $user->dia_chi ?? '' }}" style="height: 52px; border-radius: 12px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: var(--space-4); margin-bottom: var(--space-6);">
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Tỉnh/Thành phố</label>
                                <select class="form-control" name="city" id="city-select" style="height: 52px; border-radius: 12px;">
                                    <option value="">Chọn tỉnh/thành</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Quận/Huyện</label>
                                <select class="form-control" name="district" id="district-select" style="height: 52px; border-radius: 12px;">
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Phường/Xã</label>
                                <select class="form-control" name="ward" id="ward-select" style="height: 52px; border-radius: 12px;">
                                    <option value="">Chọn phường/xã</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Ghi chú (tùy chọn)</label>
                            <textarea name="ghi_chu" class="form-control" rows="4" placeholder="Ghi chú cho đơn hàng..." style="border-radius: 12px; padding: 15px;"></textarea>
                        </div>
                        
                        <div class="form-group" style="margin-top: 15px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="save_address" value="1" style="width: 18px; height: 18px; accent-color: var(--color-primary);">
                                <span>Lưu thông tin này vào Sổ địa chỉ cho lần mua sau</span>
                            </label>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light);">
                        <h2 style="margin-bottom: var(--space-8); font-size: 28px;">Phương thức thanh toán</h2>

                        <div style="display: flex; flex-direction: column; gap: var(--space-4);" id="payment-methods">
                            <label class="payment-option active" style="display: flex; align-items: center; gap: var(--space-4); padding: 20px; border: 2px solid var(--color-primary); border-radius: 15px; cursor: pointer; background: var(--color-primary-light); transition: all 0.2s;">
                                <input type="radio" name="phuong_thuc_tt" value="cod" checked style="width: 20px; height: 20px; accent-color: var(--color-primary-dark);">
                                <span class="material-icons" style="font-size: 32px; color: var(--color-primary-dark);">local_shipping</span>
                                <div>
                                    <div style="font-weight: 700; font-size: 16px;">Thanh toán khi nhận hàng (COD)</div>
                                    <div style="font-size: 13px; color: var(--color-text-muted);">Thanh toán bằng tiền mặt khi nhận sách</div>
                                </div>
                            </label>

                            <label class="payment-option" style="display: flex; align-items: center; gap: var(--space-4); padding: 20px; border: 1px solid var(--color-border); border-radius: 15px; cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="phuong_thuc_tt" value="payos" style="width: 20px; height: 20px; accent-color: var(--color-primary-dark);">
                                <span class="material-icons" style="font-size: 32px; color: var(--color-text-secondary);">qr_code_scanner</span>
                                <div>
                                    <div style="font-weight: 700; font-size: 16px;">Thanh toán QR (PayOS)</div>
                                    <div style="font-size: 13px; color: var(--color-text-muted);">Quét mã QR bằng ứng dụng ngân hàng</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Order Summary --}}
            <div class="order-summary" style="padding: var(--space-8); border-radius: var(--radius-2xl);">
                <h3 style="margin-bottom: var(--space-8); font-size: 22px;">Đơn hàng của bạn</h3>
                <div style="margin-bottom: var(--space-8);">
                    @foreach($cart as $cartItem)
                    <div style="display: flex; gap: var(--space-4); margin-bottom: var(--space-6); align-items: flex-start;">
                        <div style="width: 60px; height: 85px; background: var(--color-bg-alt); border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @php
                                $imageUrl = $cartItem['link_anh_bia'] ?: ($cartItem['file_anh_bia'] ? asset('uploads/books/' . $cartItem['file_anh_bia']) : 'https://placehold.co/300x400?text=No+Image');
                            @endphp
                            <img src="{{ $imageUrl }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="font-weight: 600; font-size: 16px; line-height: 1.4;">{{ $cartItem['tieu_de'] }}</div>
                                <div style="font-weight: 700; font-size: 16px;">{{ number_format($cartItem['thanh_tien'], 0, ',', '.') }}đ</div>
                            </div>
                            <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">x{{ $cartItem['so_luong'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid var(--color-border-light); padding-top: var(--space-6);">
                    <div class="summary-row" style="margin-bottom: 20px;">
                        <span style="color: var(--color-text-secondary);">Tạm tính</span>
                        <span style="font-weight: 600;">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="summary-row" style="margin-bottom: 20px;">
                        <span style="color: var(--color-text-secondary);">Phí vận chuyển</span>
                        <span id="shipping-fee-display" style="font-weight: 600;">{{ $phi_ship > 0 ? number_format($phi_ship, 0, ',', '.') . 'đ' : 'Miễn phí' }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="summary-row" style="margin-bottom: 25px;">
                        <span style="color: var(--color-primary-dark);">Giảm giá</span>
                        <span style="color: var(--color-primary-dark); font-weight: 600;">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    <div class="summary-row total" style="border-top: none; padding-top: 0;">
                        <span style="font-size: 22px; font-weight: 700;">Tổng cộng</span>
                        <span id="total-display" style="font-size: 24px; font-weight: 800;">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <button type="submit" form="checkout-form" class="btn btn-primary btn-block btn-lg" style="margin-top: 40px; height: 64px; font-size: 20px; font-weight: 700; border-radius: 32px;" id="btn-place-order">
                    Đặt hàng
                </button>
            </div>
        </div>
    </div>

    <script>
        // Dữ liệu địa chỉ mặc định (từ Sổ địa chỉ hoặc thông tin cá nhân)
        window._profileCity     = '{{ $defaultAddress->tinh_thanh_pho ?? "" }}';
        window._profileDistrict = '{{ $defaultAddress->quan_huyen ?? "" }}';
        window._profileWard     = '{{ $defaultAddress->phuong_xa ?? "" }}';
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const citySelect = document.getElementById('city-select');
            const districtSelect = document.getElementById('district-select');
            const wardSelect = document.getElementById('ward-select');
            const addressSelect = document.getElementById('user-address-select');

            // --- API Loading Functions ---
            async function loadProvinces() {
                try {
                    const res = await fetch('https://provinces.open-api.vn/api/p/');
                    const data = await res.json();
                    data.forEach(province => {
                        const opt = document.createElement('option');
                        opt.value = province.name; 
                        opt.textContent = province.name;
                        opt.dataset.code = province.code;
                        citySelect.appendChild(opt);
                    });
                    
                    // Tự động kéo dữ liệu Tỉnh/Huyện/Xã từ thông tin cá nhân của người dùng
                    if (window._profileCity) {
                        const cityOption = Array.from(citySelect.options).find(opt => opt.value === window._profileCity);
                        if (cityOption) {
                            citySelect.value = window._profileCity;
                            const districtCode = await loadDistricts(cityOption.dataset.code, window._profileDistrict);
                            if (districtCode) {
                                await loadWards(districtCode, window._profileWard);
                            }
                            updateShippingFee(window._profileCity);
                        }
                    }
                } catch (err) {
                    console.error('Lỗi khi tải tỉnh thành:', err);
                }
            }

            async function loadDistricts(provinceCode, targetDistrictName = null) {
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                
                if (!provinceCode) return;

                try {
                    const res = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
                    const data = await res.json();
                    let districtCode = null;
                    if (data.districts) {
                        data.districts.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.name;
                            opt.textContent = d.name;
                            opt.dataset.code = d.code;
                            if (targetDistrictName && d.name === targetDistrictName) {
                                opt.selected = true;
                                districtCode = d.code;
                            }
                            districtSelect.appendChild(opt);
                        });
                    }
                    return districtCode;
                } catch (err) {
                    console.error('Lỗi khi tải quận huyện:', err);
                    return null;
                }
            }

            async function loadWards(districtCode, targetWardName = null) {
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                if (!districtCode) return;

                try {
                    const res = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
                    const data = await res.json();
                    if (data.wards) {
                        data.wards.forEach(w => {
                            const opt = document.createElement('option');
                            opt.value = w.name;
                            opt.textContent = w.name;
                            if (targetWardName && w.name === targetWardName) {
                                opt.selected = true;
                            }
                            wardSelect.appendChild(opt);
                        });
                    }
                } catch (err) {
                    console.error('Lỗi khi tải phường xã:', err);
                }
            }

            async function updateShippingFee(city) {
                if (!city) return;
                const shippingEl = document.getElementById('shipping-fee-display');
                const totalEl = document.getElementById('total-display');
                shippingEl.innerHTML = '<span class="material-icons" style="font-size:14px; animation:spin 1s linear infinite;">sync</span> Đang tính...';
                
                try {
                    const res = await fetch('{{ route("api.shipping.fee") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ city: city })
                    });
                    const data = await res.json();
                    shippingEl.textContent = data.fee_formatted;
                    totalEl.textContent = data.total_formatted;
                } catch (err) {
                    console.error('Error fetching shipping fee', err);
                    shippingEl.textContent = 'Lỗi tính phí';
                }
            }

            // --- Event Handlers ---
            citySelect.addEventListener('change', async function() {
                const optionSelected = this.options[this.selectedIndex];
                const code = optionSelected ? optionSelected.dataset.code : null;
                await loadDistricts(code);
                updateShippingFee(this.value);
            });

            districtSelect.addEventListener('change', async function() {
                const optionSelected = this.options[this.selectedIndex];
                const code = optionSelected ? optionSelected.dataset.code : null;
                await loadWards(code);
            });

            async function fillAddressFromSelect(option) {
                if (!option || !option.value) return;
                
                document.querySelector('input[name="ho_ten"]').value = option.getAttribute('data-name') || '';
                document.querySelector('input[name="so_dien_thoai"]').value = option.getAttribute('data-phone') || '';
                document.querySelector('input[name="dia_chi"]').value = option.getAttribute('data-address') || '';
                
                const cityName = option.getAttribute('data-city');
                const districtName = option.getAttribute('data-district');
                const wardName = option.getAttribute('data-ward');

                if (cityName) {
                    citySelect.value = cityName;
                    const cityOption = Array.from(citySelect.options).find(opt => opt.value === cityName);
                    if (cityOption) {
                        const cityCode = cityOption.dataset.code;
                        const districtCode = await loadDistricts(cityCode, districtName);
                        if (districtCode) {
                            await loadWards(districtCode, wardName);
                        }
                        updateShippingFee(cityName);
                    }
                }
            }

            if (addressSelect) {
                addressSelect.addEventListener('change', function() {
                    fillAddressFromSelect(this.options[this.selectedIndex]);
                });
            }

            // --- Payment Option UI ---
            const options = document.querySelectorAll('.payment-option');
            options.forEach(opt => {
                opt.addEventListener('click', function() {
                    options.forEach(o => {
                        o.style.border = '1px solid var(--color-border)';
                        o.style.background = 'transparent';
                        o.classList.remove('active');
                        o.querySelector('.material-icons').style.color = 'var(--color-text-secondary)';
                    });

                    this.style.border = '2px solid var(--color-primary)';
                    this.style.background = 'var(--color-primary-light)';
                    this.classList.add('active');
                    this.querySelector('.material-icons').style.color = 'var(--color-primary-dark)';
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });

            // Initialize
            loadProvinces();
        });
    </script>
@endsection
