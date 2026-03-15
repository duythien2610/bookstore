@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="container" style="max-width: 680px; margin: var(--space-16) auto;">
        <div style="text-align: center; margin-bottom: var(--space-8);">
            <div style="width: 80px; height: 80px; background: rgba(76,175,80,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-4);">
                <span class="material-icons" style="font-size: 40px; color: #4caf50;">check_circle</span>
            </div>
            <h1 style="color: #4caf50; margin-bottom: var(--space-2);">Đặt hàng thành công!</h1>
            <p style="color: var(--color-text-muted);">Cảm ơn bạn đã mua hàng tại Modtra Books</p>
        </div>

        {{-- Thông tin đơn hàng --}}
        <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 1px solid var(--color-border-light); margin-bottom: var(--space-6);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
                <h3>Đơn hàng #{{ str_pad($donHang->id, 6, '0', STR_PAD_LEFT) }}</h3>
                <span class="badge {{ $donHang->trang_thai_color }}">{{ $donHang->trang_thai_label }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-4); font-size: var(--font-size-sm); margin-bottom: var(--space-4);">
                <div>
                    <div style="color: var(--color-text-muted);">Người nhận</div>
                    <div style="font-weight: var(--font-medium);">{{ $donHang->ho_ten }}</div>
                </div>
                <div>
                    <div style="color: var(--color-text-muted);">Số điện thoại</div>
                    <div>{{ $donHang->so_dien_thoai }}</div>
                </div>
                <div style="grid-column: span 2;">
                    <div style="color: var(--color-text-muted);">Địa chỉ giao hàng</div>
                    <div>{{ $donHang->dia_chi_giao }}</div>
                </div>
                <div>
                    <div style="color: var(--color-text-muted);">Phương thức thanh toán</div>
                    <div>{{ match($donHang->phuong_thuc_tt) { 'cod' => 'Thanh toán khi nhận hàng', 'bank' => 'Chuyển khoản ngân hàng', 'momo' => 'Ví MoMo', default => $donHang->phuong_thuc_tt } }}</div>
                </div>
                <div>
                    <div style="color: var(--color-text-muted);">Ngày đặt</div>
                    <div>{{ $donHang->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            {{-- Sản phẩm --}}
            <div style="border-top: 1px solid var(--color-border-light); padding-top: var(--space-4);">
                @foreach ($donHang->chiTiets as $ct)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-2) 0; font-size: var(--font-size-sm);">
                    <div>
                        <span style="font-weight: var(--font-medium);">{{ $ct->sach->tieu_de ?? 'N/A' }}</span>
                        <span style="color: var(--color-text-muted);"> x{{ $ct->so_luong }}</span>
                    </div>
                    <span>{{ number_format($ct->thanh_tien, 0, ',', '.') }}đ</span>
                </div>
                @endforeach

                <div style="border-top: 1px solid var(--color-border-light); margin-top: var(--space-3); padding-top: var(--space-3);">
                    @if($donHang->giam_gia > 0)
                    <div style="display: flex; justify-content: space-between; font-size: var(--font-size-sm); color: var(--color-primary-dark);">
                        <span>Giảm giá</span><span>-{{ number_format($donHang->giam_gia, 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; font-size: var(--font-size-sm);">
                        <span>Phí vận chuyển</span>
                        <span>{{ $donHang->phi_van_chuyen == 0 ? 'Miễn phí' : number_format($donHang->phi_van_chuyen, 0, ',', '.') . 'đ' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: var(--font-bold); font-size: var(--font-size-lg); margin-top: var(--space-2);">
                        <span>Tổng cộng</span>
                        <span style="color: var(--color-primary-dark);">{{ number_format($donHang->thanh_toan, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Casso QR (nếu chuyển khoản) --}}
        @if ($cassoInfo)
        <div style="background: var(--color-white); border-radius: var(--radius-xl); padding: var(--space-6); border: 2px solid var(--color-primary); margin-bottom: var(--space-6); text-align: center;">
            <h3 style="margin-bottom: var(--space-4);">💳 Thông tin chuyển khoản</h3>
            <p style="color: var(--color-text-muted); margin-bottom: var(--space-4); font-size: var(--font-size-sm);">
                Vui lòng chuyển khoản với nội dung chính xác để Casso tự động xác nhận đơn hàng.
            </p>
            <div style="background: var(--color-bg-alt); border-radius: var(--radius-lg); padding: var(--space-4); text-align: left; font-size: var(--font-size-sm); margin-bottom: var(--space-4);">
                <div style="display: flex; justify-content: space-between; padding: var(--space-2) 0;"><strong>Ngân hàng:</strong><span>{{ $cassoInfo['bank_name'] }}</span></div>
                <div style="display: flex; justify-content: space-between; padding: var(--space-2) 0;"><strong>Số tài khoản:</strong><span style="color: var(--color-primary-dark); font-weight: var(--font-bold);">{{ $cassoInfo['account_no'] }}</span></div>
                <div style="display: flex; justify-content: space-between; padding: var(--space-2) 0;"><strong>Chủ tài khoản:</strong><span>{{ $cassoInfo['account_name'] }}</span></div>
                <div style="display: flex; justify-content: space-between; padding: var(--space-2) 0;"><strong>Số tiền:</strong><span style="color: #e53e3e; font-weight: var(--font-bold);">{{ number_format($cassoInfo['amount'], 0, ',', '.') }}đ</span></div>
                <div style="display: flex; justify-content: space-between; padding: var(--space-2) 0; border-top: 1px dashed var(--color-border); margin-top: var(--space-2);">
                    <strong>Nội dung CK:</strong>
                    <span id="ck-content" style="color: var(--color-primary-dark); font-weight: var(--font-bold);">{{ $cassoInfo['description'] }}</span>
                </div>
            </div>
            <button onclick="copyContent()" class="btn btn-outline btn-sm">
                <span class="material-icons" style="font-size: 16px;">content_copy</span> Copy nội dung CK
            </button>
            <p style="font-size: var(--font-size-xs); color: var(--color-text-muted); margin-top: var(--space-3);">
                ⚡ Sau khi chuyển khoản, đơn hàng sẽ được xác nhận tự động trong vài phút.
            </p>
        </div>
        @endif

        <div style="display: flex; gap: var(--space-4);">
            <a href="{{ route('order.tracking', $donHang->id) }}" class="btn btn-primary" style="flex: 1; text-align: center;">
                <span class="material-icons">local_shipping</span> Theo dõi đơn hàng
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline" style="flex: 1; text-align: center;">
                <span class="material-icons">auto_stories</span> Tiếp tục mua sắm
            </a>
        </div>
    </div>

@push('scripts')
<script>
function copyContent() {
    const text = document.getElementById('ck-content').textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Đã copy nội dung: ' + text);
    });
}
</script>
@endpush
@endsection
