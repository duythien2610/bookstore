<?php

namespace App\Services;

class ShippingService
{
    /**
     * Giả lập API tính phí ship của đơn vị vận chuyển (GHTK, GHN, Viettel Post)
     * @param string $tinhThanh
     * @param float $subtotal
     * @return int
     */
    public function calculateFee($tinhThanh, $subtotal)
    {
        // Chính sách: Freeship cho đơn từ 500k trở lên
        if ($subtotal >= 500000) {
            return 0;
        }

        $tinhThanh = mb_strtolower(trim($tinhThanh), 'UTF-8');

        // Nếu ở TP. Hồ Chí Minh hoặc Hà Nội
        if (str_contains($tinhThanh, 'hồ chí minh') || str_contains($tinhThanh, 'hcm')) {
            return 20000;
        }
        if (str_contains($tinhThanh, 'hà nội') || str_contains($tinhThanh, 'hn')) {
            return 25000;
        }

        // Các tỉnh thành phố khác
        return 35000; // Mock: Đồng giá 35k vùng xa
    }
}
