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
            $status = $request->query('status'); // 'cancel' or 'success' có thể từ URL
            
            // NEVER TRUST CLIENT URL PARAMS -> Gọi trực tiếp qua Server-to-Server API của PayOS để check
            $paymentInfo = $this->payOS->getPaymentLinkInformation($orderCode);
            
            if ($paymentInfo['status'] === 'PAID') {
                if ($donHang->trang_thai_tt !== 'da_thanh_toan') {
                    $donHang->update([
                        'trang_thai_tt' => 'da_thanh_toan',
                        'trang_thai'    => 'cho_xac_nhan',
                    ]);
                    
                    try {
                        \App\Jobs\SendOrderConfirmationEmail::dispatch($donHang);
                    } catch (\Exception $e) {
                         Log::error('Lỗi khi gửi email xác nhận PayOS: ' . $e->getMessage());
                    }
                }
                return redirect()->route('order.success', ['order_id' => $donHang->id])
                    ->with('success', 'Thanh toán PayOS thành công!');
            } else {
                // Nếu status = CANCELLED (hoặc user tự back về URL có status=cancel)
                $donHang->update([
                    'trang_thai_tt' => 'that_bai', 
                    'trang_thai'    => 'huy'
                ]);
                return redirect()->route('order.tracking', ['id' => $donHang->id])
                    ->with('error', 'Thanh toán đã bị hủy hoặc chưa hoàn tất. Đơn hàng đã bị huỷ.');
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi verify PayOS return: ' . $e->getMessage());
            // Fallback: nếu gọi API lỗi và URL có status=cancel thì cập nhật huỷ luôn
            if ($request->query('status') === 'cancel') {
                 $donHang->update([
                    'trang_thai_tt' => 'that_bai', 
                    'trang_thai'    => 'huy'
                ]);
                return redirect()->route('order.tracking', ['id' => $donHang->id])
                    ->with('error', 'Bạn đã huỷ giao dịch thanh toán. Đơn hàng đã chuyển sang trạng thái huỷ.');
            }

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
                
                try {
                    \App\Jobs\SendOrderConfirmationEmail::dispatch($donHang);
                } catch (\Exception $e) {
                     Log::error('Lỗi khi gửi email qua Webhook PayOS: ' . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Ok', 'code' => '00']);
        } catch (\Exception $e) {
            Log::error('PayOS Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
