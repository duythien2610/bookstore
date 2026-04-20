@extends('layouts.app')

@section('title', 'Theo dõi đơn hàng #' . str_pad($donHang->id, 6, '0', STR_PAD_LEFT))

@section('content')
    <div class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span class="separator">›</span>
                <span>Theo dõi đơn hàng</span>
            </div>
            <h1>Đơn hàng #{{ str_pad($donHang->id, 6, '0', STR_PAD_LEFT) }}</h1>
        </div>
    </div>

    <div class="container">
        <div class="cart-grid" id="order-tracking">
            {{-- Timeline --}}
            <div>
                <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-8); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6);">
                        <h3>Trạng thái đơn hàng</h3>
                        <span class="badge {{ $donHang->trang_thai_color }}">{{ $donHang->trang_thai_label }}</span>
                    </div>

                    @php
                        $steps = [
                            ['key' => 'dat_hang',    'label' => 'Đơn hàng đã được đặt',    'time' => $donHang->created_at->format('d/m/Y H:i')],
                            ['key' => 'xac_nhan',    'label' => 'Đã xác nhận đơn hàng',    'time' => ''],
                            ['key' => 'dong_goi',    'label' => 'Đang đóng gói',            'time' => ''],
                            ['key' => 'dang_giao',   'label' => 'Đang giao hàng',           'time' => ''],
                            ['key' => 'da_giao',     'label' => 'Giao hàng thành công',     'time' => ''],
                        ];
                        $currentStep = match($donHang->trang_thai) {
                            'cho_thanh_toan','cho_xac_nhan' => 0,
                            'dang_xu_ly'  => 2,
                            'dang_giao'   => 3,
                            'da_giao'     => 4,
                            default       => 0,
                        };
                    @endphp

                    <div class="timeline">
                        @foreach ($steps as $i => $step)
                        <div class="timeline-item {{ $i < $currentStep ? 'completed' : ($i === $currentStep ? 'active' : '') }}">
                            <div class="timeline-dot {{ $i <= $currentStep ? 'completed' : '' }}"></div>
                            <h4>{{ $step['label'] }}</h4>
                            <p>{{ $step['time'] ?: ($i <= $currentStep ? 'Hoàn thành' : 'Chờ xử lý') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Items --}}
                <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light);">
                    <h3 style="margin-bottom: var(--space-6);">Sản phẩm trong đơn</h3>
                    @foreach ($donHang->chiTiets as $ct)
                    @php $borderClass = !$loop->last ? 'border-b border-light' : ''; @endphp
                    <div style="display: flex; gap: var(--space-4); padding: var(--space-4) 0;" class="{{ $borderClass }}">
                        <div style="width: 70px; height: 90px; background: var(--color-bg-alt); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                            @if ($ct->sach && $ct->sach->file_anh_bia)
                                <img src="{{ asset('uploads/books/' . $ct->sach->file_anh_bia) }}" style="width:100%;height:100%;object-fit:cover;">
                            @elseif ($ct->sach && $ct->sach->link_anh_bia)
                                <img src="{{ $ct->sach->link_anh_bia }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'">
                            @else
                                <span class="material-icons" style="color: var(--color-text-muted);">book</span>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: var(--font-medium); margin-bottom: var(--space-1);">{{ $ct->sach->tieu_de ?? 'N/A' }}</div>
                            <div style="font-size: var(--font-size-sm); color: var(--color-text-muted);">
                                {{ $ct->sach->tacGia->ten_tac_gia ?? '' }} · x{{ $ct->so_luong }}
                            </div>
                        </div>
                        <div style="font-weight: var(--font-semibold);">{{ number_format($ct->thanh_tien, 0, ',', '.') }}đ</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Order Info --}}
            <div class="order-summary" id="order-info">
                <h3>Thông tin đơn hàng</h3>
                <div style="font-size: var(--font-size-sm); display: flex; flex-direction: column; gap: var(--space-4);">
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Mã đơn hàng</div>
                        <div style="font-weight: var(--font-semibold);">#{{ str_pad($donHang->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Người nhận</div>
                        <div style="font-weight: var(--font-medium);">{{ $donHang->ho_ten }}</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Số điện thoại</div>
                        <div>{{ $donHang->so_dien_thoai }}</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Địa chỉ giao hàng</div>
                        <div>{{ $donHang->dia_chi_giao }}</div>
                    </div>
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Thanh toán</div>
                        <div>{{ match($donHang->phuong_thuc_tt) { 'cod' => 'COD — Tiền mặt', 'bank' => 'Chuyển khoản ngân hàng', 'momo' => 'Ví MoMo', default => $donHang->phuong_thuc_tt } }}</div>
                    </div>
                    @if($donHang->ghi_chu)
                    <div>
                        <div style="color: var(--color-text-muted); margin-bottom: var(--space-1);">Ghi chú</div>
                        <div>{{ $donHang->ghi_chu }}</div>
                    </div>
                    @endif
                </div>

                <div style="border-top: 1px solid var(--color-border-light); margin-top: var(--space-6); padding-top: var(--space-4);">
                    <div class="summary-row"><span>Tạm tính</span><span>{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</span></div>
                    @if($donHang->giam_gia > 0)
                    <div class="summary-row"><span style="color: var(--color-primary-dark);">Giảm giá</span><span style="color: var(--color-primary-dark);">-{{ number_format($donHang->giam_gia, 0, ',', '.') }}đ</span></div>
                    @endif
                    <div class="summary-row"><span>Phí vận chuyển</span><span>{{ $donHang->phi_van_chuyen == 0 ? 'Miễn phí' : number_format($donHang->phi_van_chuyen, 0, ',', '.') . 'đ' }}</span></div>
                    <div class="summary-row total"><span>Tổng cộng</span><span>{{ number_format($donHang->thanh_toan, 0, ',', '.') }}đ</span></div>
                </div>
            </div>
        </div>
    </div>
@endsection
