<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonHang;
use PayOS\PayOS;
use Illuminate\Support\Facades\Log;

class PayosController extends Controller
{
    private $payOS;

    public function __construct()
    {
        $this->payOS = new PayOS(
            config('services.payos.client_id'),
            config('services.payos.api_key'),
            config('services.payos.checksum_key')
        );
    }

    public function handlePayosReturn(Request $request)
    {
        $orderCode = $request->query('orderCode');

        if (!$orderCode) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng.');
        }

        $donHang = DonHang::where('payos_order_code', $orderCode)->first();
        if (!$donHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng trong hệ thống.');
        }

        try {
            // NEVER TRUST CLIENT URL PARAMS -> Gọi trực tiếp qua Server-to-Server API của PayOS để check
            $paymentInfo = $this->payOS->getPaymentLinkInformation($orderCode);
            
            if ($paymentInfo['status'] === 'PAID') {
                if ($donHang->trang_thai_tt !== 'da_thanh_toan') {
                    $donHang->update([
                        'trang_thai_tt' => 'da_thanh_toan',
                        'trang_thai'    => 'cho_xac_nhan',
                    ]);
                }
                return redirect()->route('order.success', ['order_id' => $donHang->id])
                    ->with('success', 'Thanh toán PayOS thành công!');
            } else {
                $donHang->update(['trang_thai_tt' => 'chua_thanh_toan']);
                return redirect()->route('order.tracking', ['id' => $donHang->id])
                    ->with('error', 'Thanh toán đã bị hủy hoặc chưa hoàn tất.');
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi verify PayOS return: ' . $e->getMessage());
            return redirect()->route('order.tracking', ['id' => $donHang->id])
                ->with('error', 'Đang chờ xác nhận thanh toán hoặc có lỗi xảy ra.');
        }
    }

    // Webhook - Xử lý IPN từ PayOS
    public function handleWebhook(Request $request)
    {
        try {
            $body = $request->getContent();
            $webhookBody = json_decode($body, true);

            if (!$webhookBody || empty($webhookBody['data'])) {
                return response()->json(['error' => true, 'message' => 'Invalid data payload'], 400);
            }

            // Verify webhook data (payos SDK cung cấp hàm verifyPaymentWebhookData nhưng phải parse Request)
            $webhookData = $this->payOS->verifyPaymentWebhookData($webhookBody);

            $orderCode = $webhookData['orderCode'];
            $donHang = DonHang::where('payos_order_code', $orderCode)->first();

            if ($donHang && $donHang->trang_thai_tt !== 'da_thanh_toan') {
                $donHang->update([
                    'trang_thai_tt' => 'da_thanh_toan',
                    'trang_thai'    => 'cho_xac_nhan',
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Ok', 'code' => '00']);
        } catch (\Exception $e) {
            Log::error('PayOS Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
