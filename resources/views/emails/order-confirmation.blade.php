<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #1a202c; }
        .header h1 { color: #1a202c; margin: 0; font-size: 24px; }
        .content { padding: 30px 0; }
        .order-info { background: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .order-info h3 { margin-top: 0; color: #1a202c; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        table th { color: #718096; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .total-row td { border-bottom: none; font-weight: bold; }
        .footer { text-align: center; color: #718096; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #1a202c; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BOOKVERSE</h1>
            <p>Cảm ơn bạn đã đặt hàng!</p>
        </div>

        <div class="content">
            <p>Xin chào <strong>{{ $donHang->ho_ten }}</strong>,</p>
            <p>Đơn hàng của bạn đã được tiếp nhận và đang trong quá trình xử lý. Dưới đây là thông tin chi tiết về đơn hàng mã <strong>#{{ str_pad($donHang->id, 6, '0', STR_PAD_LEFT) }}</strong>.</p>

            <div class="order-info">
                <h3>Thông tin giao hàng</h3>
                <p style="margin: 5px 0;"><strong>SĐT:</strong> {{ $donHang->so_dien_thoai }}</p>
                <p style="margin: 5px 0;"><strong>Địa chỉ:</strong> {{ $donHang->dia_chi_giao }}</p>
                <p style="margin: 5px 0;"><strong>Phương thức:</strong> {{ strtoupper($donHang->phuong_thuc_tt) }}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="text-align: right;">SL</th>
                        <th style="text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donHang->chiTiets as $item)
                    <tr>
                        <td>{{ $item->sach->tieu_de ?? 'Sản phẩm' }}</td>
                        <td style="text-align: right;">{{ $item->so_luong }}</td>
                        <td style="text-align: right;">{{ number_format($item->thanh_tien, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;">Tạm tính:</td>
                        <td style="text-align: right;">{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right;">Phí ship:</td>
                        <td style="text-align: right;">{{ number_format($donHang->phi_van_chuyen, 0, ',', '.') }}đ</td>
                    </tr>
                    @if($donHang->giam_gia > 0)
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; color: #e53e3e;">Giảm giá:</td>
                        <td style="text-align: right; color: #e53e3e;">-{{ number_format($donHang->giam_gia, 0, ',', '.') }}đ</td>
                    </tr>
                    @endif
                    <tr class="total-row" style="font-size: 18px;">
                        <td colspan="2" style="text-align: right;">Tổng thanh toán:</td>
                        <td style="text-align: right; color: #1a202c;">{{ number_format($donHang->thanh_toan, 0, ',', '.') }}đ</td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center;">
                <p>Bạn có thể theo dõi trạng thái đơn hàng của mình bất cứ lúc nào tại website của chúng tôi.</p>
                <a href="{{ route('order.tracking', ['id' => $donHang->id]) }}" class="btn">Theo dõi đơn hàng</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Bookverse. All rights reserved.</p>
            <p>Số 1, Đại Cồ Việt, Hai Bà Trưng, Hà Nội | Hotline: 1900 1234</p>
        </div>
    </div>
</body>
</html>
