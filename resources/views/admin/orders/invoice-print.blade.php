<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Hóa Đơn #{{ $donHang->id }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.5; font-size: 14px; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; color: #1a202c; }
        .details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .details div { width: 48%; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        table th { background: #f8f9fa; }
        .text-right { text-align: right; }
        .total-box { margin-left: auto; width: 300px; border-top: 2px solid #333; padding-top: 10px; }
        .total-box div { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total-box .final { font-weight: bold; font-size: 18px; margin-top: 10px; }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; border-radius: 5px; cursor: pointer;">🖨️ In Hóa Đơn Mày</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>BOOKVERSE</h1>
                <p style="margin: 5px 0 0; color: #666;">Số 1, Đại Cồ Việt, Hai Bà Trưng, Hà Nội</p>
                <p style="margin: 0; color: #666;">Điện thoại: 1900 1234</p>
            </div>
            <div style="text-align: right;">
                <h2 style="margin: 0; color: #e53e3e;">HÓA ĐƠN BÁN LẺ</h2>
                <p style="margin: 5px 0 0;">Mã đơn: <strong>#{{ str_pad($donHang->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                <p style="margin: 0;">Ngày đặt: {{ \Carbon\Carbon::parse($donHang->ngay_dat)->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="details">
            <div>
                <strong>Thông tin khách hàng:</strong>
                <p style="margin: 5px 0;">Tên: {{ $donHang->ho_ten }}</p>
                <p style="margin: 0;">SĐT: {{ $donHang->so_dien_thoai }}</p>
                <p style="margin: 0;">Địa chỉ: {{ $donHang->dia_chi_giao }}</p>
            </div>
            <div>
                <strong>Ghi chú:</strong>
                <p style="margin: 5px 0;">{{ $donHang->ghi_chu ?: 'Không có' }}</p>
                <strong>Thanh toán:</strong>
                <p style="margin: 0;">{{ strtoupper($donHang->phuong_thuc_tt) }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên Sách</th>
                    <th class="text-right">SL</th>
                    <th class="text-right">Đơn giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donHang->chiTiets as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->sach->tieu_de ?? 'Sách đã bị xóa' }}</td>
                    <td class="text-right">{{ $item->so_luong }}</td>
                    <td class="text-right">{{ number_format($item->don_gia, 0, ',', '.') }}đ</td>
                    <td class="text-right">{{ number_format($item->thanh_tien, 0, ',', '.') }}đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <div>
                <span>Tạm tính:</span>
                <span>{{ number_format($donHang->tong_tien, 0, ',', '.') }}đ</span>
            </div>
            <div>
                <span>Phí vận chuyển:</span>
                <span>{{ number_format($donHang->phi_van_chuyen, 0, ',', '.') }}đ</span>
            </div>
            @if($donHang->giam_gia > 0)
            <div>
                <span>Giảm giá:</span>
                <span style="color: #e53e3e;">-{{ number_format($donHang->giam_gia, 0, ',', '.') }}đ</span>
            </div>
            @endif
            <div class="final">
                <span>Tổng phải thu:</span>
                <span>{{ number_format($donHang->thanh_toan, 0, ',', '.') }}đ</span>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 50px; font-style: italic; color: #666;">
            Cảm ơn quý khách đã mua sắm tại Bookverse!<br>
            Mọi thắc mắc xin liên hệ 1900 1234.
        </div>
    </div>
</body>
</html>
