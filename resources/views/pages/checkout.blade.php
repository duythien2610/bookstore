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
                <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
                    @csrf
                    {{-- Shipping Info --}}
                    <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
                        <h2 style="margin-bottom: var(--space-8); font-size: 28px;">Thông tin giao hàng</h2>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6); margin-bottom: var(--space-6);">
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Họ và tên</label>
                                <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" value="{{ auth()->user()->name }}" required style="height: 52px; border-radius: 12px;">
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Số điện thoại</label>
                                <input type="tel" name="dien_thoai" class="form-control" placeholder="0912 345 678" required style="height: 52px; border-radius: 12px;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: var(--space-6);">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Địa chỉ</label>
                            <input type="text" name="dia_chi_giao" class="form-control" placeholder="Số nhà, đường..." required style="height: 52px; border-radius: 12px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: var(--space-4); margin-bottom: var(--space-6);">
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Tỉnh/Thành phố</label>
                                <select class="form-control" name="city" id="city-select" required style="height: 52px; border-radius: 12px;">
                                    <option value="">Chọn tỉnh/thành</option>
                                    <option value="hcm">TP. Hồ Chí Minh</option>
                                    <option value="hn">Hà Nội</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Quận/Huyện</label>
                                <select class="form-control" name="district" id="district-select" required style="height: 52px; border-radius: 12px;">
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Phường/Xã</label>
                                <select class="form-control" name="ward" id="ward-select" required style="height: 52px; border-radius: 12px;">
                                    <option value="">Chọn phường/xã</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Ghi chú (tùy chọn)</label>
                            <textarea name="ghi_chu" class="form-control" rows="4" placeholder="Ghi chú cho đơn hàng..." style="border-radius: 12px; padding: 15px;"></textarea>
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
                                <input type="radio" name="phuong_thuc_tt" value="bank" style="width: 20px; height: 20px; accent-color: var(--color-primary-dark);">
                                <span class="material-icons" style="font-size: 32px; color: var(--color-text-secondary);">account_balance</span>
                                <div>
                                    <div style="font-weight: 700; font-size: 16px;">Chuyển khoản ngân hàng</div>
                                    <div style="font-size: 13px; color: var(--color-text-muted);">Chuyển khoản qua ngân hàng nội địa</div>
                                </div>
                            </label>

                            <label class="payment-option" style="display: flex; align-items: center; gap: var(--space-4); padding: 20px; border: 1px solid var(--color-border); border-radius: 15px; cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="phuong_thuc_tt" value="momo" style="width: 20px; height: 20px; accent-color: var(--color-primary-dark);">
                                <span class="material-icons" style="font-size: 32px; color: var(--color-text-secondary);">phone_iphone</span>
                                <div>
                                    <div style="font-weight: 700; font-size: 16px;">Ví MoMo</div>
                                    <div style="font-size: 13px; color: var(--color-text-muted);">Thanh toán qua ví điện tử MoMo</div>
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
                    @foreach($items as $item)
                    <div style="display: flex; gap: var(--space-4); margin-bottom: var(--space-6); align-items: flex-start;">
                        <div style="width: 60px; height: 85px; background: var(--color-bg-alt); border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @php
                                $imageUrl = $item->sach->link_anh_bia ?: ($item->sach->file_anh_bia ? asset('uploads/books/' . $item->sach->file_anh_bia) : 'https://placehold.co/300x400?text=No+Image');
                            @endphp
                            <img src="{{ $imageUrl }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="font-weight: 600; font-size: 16px; line-height: 1.4;">{{ $item->sach->tieu_de }}</div>
                                <div style="font-weight: 700; font-size: 16px;">{{ number_format($item->thanh_tien, 0, ',', '.') }}đ</div>
                            </div>
                            <div style="font-size: 13px; color: var(--color-text-muted); margin-top: 4px;">x{{ $item->so_luong }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid var(--color-border-light); padding-top: var(--space-6);">
                    <div class="summary-row" style="margin-bottom: 20px;">
                        <span style="color: var(--color-text-secondary);">Tạm tính</span>
                        <span style="font-weight: 600;">{{ number_format($gioHang->tong_tien, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="summary-row" style="margin-bottom: 20px;">
                        <span style="color: var(--color-text-secondary);">Phí vận chuyển</span>
                        <span style="font-weight: 600;">30.000đ</span>
                    </div>
                    <div class="summary-row" style="margin-bottom: 25px;">
                        <span style="color: var(--color-primary-dark);">Giảm giá</span>
                        <span style="color: var(--color-primary-dark); font-weight: 600;">-50.000đ</span>
                    </div>
                    <div class="summary-row total" style="border-top: none; padding-top: 0;">
                        <span style="font-size: 22px; font-weight: 700;">Tổng cộng</span>
                        <span style="font-size: 24px; font-weight: 800;">{{ number_format($gioHang->tong_tien + 30000 - 50000, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <button type="submit" form="checkout-form" class="btn btn-primary btn-block btn-lg" style="margin-top: 40px; height: 64px; font-size: 20px; font-weight: 700; border-radius: 32px;" id="btn-place-order">
                    Đặt hàng
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu mẫu Quận/Phường (Simple version for demo)
            const districts = {
                'hcm': ['Quận 1', 'Quận 3', 'Quận 5', 'Quận 7', 'Quận 10', 'Quận Bình Thạnh', 'Quận Gò Vấp', 'Thành phố Thủ Đức'],
                'hn': ['Quận Ba Đình', 'Quận Hoàn Kiếm', 'Quận Tây Hồ', 'Quận Cầu Giấy', 'Quận Đống Đa', 'Quận Hai Bà Trưng']
            };

            const wards = {
                'Quận 1': ['Phường Bến Nghé', 'Phường Bến Thành', 'Phường Cô Giang', 'Phường Cầu Kho', 'Phường Cầu Ông Lãnh', 'Phường Đa Kao', 'Phường Nguyễn Cư Trinh', 'Phường Nguyễn Thái Bình', 'Phường Phạm Ngũ Lão', 'Phường Tân Định'],
                'Quận 3': ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường Võ Thị Sáu'],
                'Quận 5': ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7', 'Phường 8'],
                'Quận 7': ['Phường Bình Thuận', 'Phường Phú Mỹ', 'Phường Phú Thuận', 'Phường Tân Hưng', 'Phường Tân Kiểng', 'Phường Tân Phong', 'Phường Tân Phú', 'Phường Tân Quy', 'Phường Tân Thuận Đông', 'Phường Tân Thuận Tây'],
                'Quận 10': ['Phường 1', 'Phường 2', 'Phường 4', 'Phường 5', 'Phường 6', 'Phường 7', 'Phường 8', 'Phường 9', 'Phường 10', 'Phường 11', 'Phường 12', 'Phường 13', 'Phường 14', 'Phường 15'],
                'Quận Ba Đình': ['Phường Cống Vị', 'Phường Điện Biên', 'Phường Đội Cấn', 'Phường Giảng Võ', 'Phường Kim Mã', 'Phường Liễu Giai', 'Phường Ngọc Hà', 'Phường Ngọc Khánh', 'Phường Nguyễn Trung Trực', 'Phường Phúc Xá', 'Phường Quán Thánh', 'Phường Thành Công', 'Phường Trúc Bạch', 'Phường Vĩnh Phúc'],
                'Quận Hoàn Kiếm': ['Phường Chương Dương', 'Phường Cửa Đông', 'Phường Cửa Nam', 'Phường Đồng Xuân', 'Phường Hàng Bạc', 'Phường Hàng Bài', 'Phường Hàng Bồ', 'Phường Hàng Bông', 'Phường Hàng Buồm', 'Phường Hàng Đào', 'Phường Hàng Gai', 'Phường Hàng Mã', 'Phường Hàng Trống', 'Phường Lý Thái Tổ', 'Phường Phan Chu Trinh', 'Phường Phúc Tân', 'Phường Tràng Tiền']
            };
            
            const citySelect = document.getElementById('city-select');
            const districtSelect = document.getElementById('district-select');
            const wardSelect = document.getElementById('ward-select');

            citySelect.addEventListener('change', function() {
                const city = this.value;
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                
                if (districts[city]) {
                    districts[city].forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        districtSelect.appendChild(opt);
                    });
                }
            });

            districtSelect.addEventListener('change', function() {
                const district = this.value;
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                
                if (wards[district]) {
                    wards[district].forEach(w => {
                        const opt = document.createElement('option');
                        opt.value = w;
                        opt.textContent = w;
                        wardSelect.appendChild(opt);
                    });
                } else if (districts['hcm'].concat(districts['hn']).includes(district)) {
                   // Default fallback if some districts don't have ward data yet
                   for(let i=1; i<=5; i++) {
                        const opt = document.createElement('option');
                        opt.value = 'Phường ' + i;
                        opt.textContent = 'Phường ' + i;
                        wardSelect.appendChild(opt);
                    }
                }
            });

            // Xử lý di chuyển vùng chọn màu xanh cho Phương thức thanh toán
            const options = document.querySelectorAll('.payment-option');
            options.forEach(opt => {
                opt.addEventListener('click', function() {
                    // Xóa trạng thái active của tất cả các ô
                    options.forEach(o => {
                        o.style.border = '1px solid var(--color-border)';
                        o.style.background = 'transparent';
                        o.classList.remove('active');
                        o.querySelector('.material-icons').style.color = 'var(--color-text-secondary)';
                    });

                    // Thêm trạng thái active cho ô hiện tại
                    this.style.border = '2px solid var(--color-primary)';
                    this.style.background = 'var(--color-primary-light)';
                    this.classList.add('active');
                    this.querySelector('.material-icons').style.color = 'var(--color-primary-dark)';
                    
                    // Đồng bộ radio button
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });
        });
    </script>
@endsection
