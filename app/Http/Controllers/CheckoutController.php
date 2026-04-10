<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\GioHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // =========================================================================
    //  Chuẩn bị checkout mảng thiết bị được chọn
    // =========================================================================
    public function prepare(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);
        if (empty($selectedItems)) {
            return redirect()->route('cart')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
        }

        session(['checkout_items' => $selectedItems]);
        return redirect()->route('checkout');
    }

    // =========================================================================
    //  Hiển thị trang checkout
    // =========================================================================

    public function show()
    {
        $fullCart = session('cart', []);
        $checkoutItems = session('checkout_items', []);
        $discount = session('cart_discount', 0);

        if (empty($fullCart) || empty($checkoutItems)) {
            return redirect()->route('cart')->with('error', 'Không có sản phẩm nào được chọn để thanh toán!');
        }

        $cart = [];
        $subtotal = 0;
        foreach ($checkoutItems as $key) {
            if (isset($fullCart[$key])) {
                $cart[$key] = $fullCart[$key];
                $subtotal += $fullCart[$key]['gia_ban'] * $fullCart[$key]['so_luong'];
            }
        }

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Sản phẩm đã chọn không hợp lệ hoặc đã bị xóa!');
        }

        $phi_ship   = $subtotal >= 300000 ? 0 : 30000;
        $total      = max(0, $subtotal - $discount) + $phi_ship;
        $user       = Auth::user();

        // Thông tin Casso QR thanh toán
        $cassoInfo = [
            'bank_name'   => config('payment.casso_bank_name', 'MB Bank'),
            'account_no'  => config('payment.casso_account_no', ''),
            'account_name'=> config('payment.casso_account_name', 'Modtra Books'),
        ];

        return view('pages.checkout', compact('cart', 'subtotal', 'discount', 'phi_ship', 'total', 'user', 'cassoInfo'));
    }

    // =========================================================================
    //  Xử lý đặt hàng
    // =========================================================================

    public function store(Request $request)
    {
        $fullCart = session('cart', []);
        $checkoutItems = session('checkout_items', []);

        if (empty($fullCart) || empty($checkoutItems)) {
            return redirect()->route('cart')->with('error', 'Không có sản phẩm hợp lệ để thanh toán!');
        }

        $cart = [];
        $subtotal = 0;
        foreach ($checkoutItems as $key) {
            if (isset($fullCart[$key])) {
                $cart[$key] = $fullCart[$key];
                $subtotal += $fullCart[$key]['gia_ban'] * $fullCart[$key]['so_luong'];
            }
        }

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Sản phẩm đã chọn không tồn tại trong giỏ hàng!');
        }

        $request->validate([
            'ho_ten'         => 'required|string|max:100',
            'so_dien_thoai'  => 'required|string|max:15',
            'dia_chi'        => 'required|string|max:500',
            'phuong_thuc_tt' =>'required|in:cod,payos',
            'idempotency_key'=> 'required|string',
            'website_url'    => 'nullable|string|max:0', // HONEYPOT (Phải rỗng)
        ], [
            'ho_ten.required'         => 'Vui lòng nhập họ và tên.',
            'so_dien_thoai.required'  => 'Vui lòng nhập số điện thoại.',
            'dia_chi.required'        => 'Vui lòng nhập địa chỉ giao hàng.',
            'phuong_thuc_tt.required' => 'Vui lòng chọn phương thức thanh toán.',
            'website_url.max'         => 'Hành vi đánh giá là SPAM BOT. Yêu cầu bị từ chối!',
        ]);

        // CHỐNG ORDER FRAUD (Giới hạn 1 tài khoản tối đa 5 đơn hàng đang chờ xử lý)
        $pendingOrders = \App\Models\DonHang::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereIn('trang_thai', ['cho_xac_nhan', 'cho_thanh_toan', 'dang_xu_ly'])
            ->count();
            
        if ($pendingOrders >= 5) {
            return back()->with('error', 'Bạn đang có quá nhiều đơn hàng chờ xử lý. Vui lòng thanh toán hoặc chờ xác nhận trước khi đặt thêm!');
        }

        // CHỐNG SPAM THEO SỐ LƯỢNG ĐƠN / NGÀY TỪ 1 USER
        $dailyOrders = \App\Models\DonHang::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereDate('created_at', \Illuminate\Support\Carbon::today())
            ->count();
            
        if ($dailyOrders >= 20) {
            return back()->with('error', 'Tài khoản của bạn đã đạt giới hạn 20 đơn hàng/ngày. Vui lòng thử lại vào ngày mai!');
        }

        $discount   = session('cart_discount', 0);
        $phi_ship   = $subtotal >= 300000 ? 0 : 30000;
        $tong_tien  = $subtotal + $phi_ship - $discount;

        // XỬ LÝ IDEMPOTENCY KEY (Chống Double Click bằng Atomic Cache Lock)
        $lock = \Illuminate\Support\Facades\Cache::lock('checkout_lock_' . $request->idempotency_key, 10);
        if (!$lock->get()) {
            return back()->with('error', 'Hệ thống đang xử lý đơn hàng của bạn, vui lòng không nhấn nhiều lần!');
        }

        try {
            DB::beginTransaction();

            // Ràng buộc kiểm tra mã giảm giá NGAY TRONG GIAO DỊCH (Tránh Race Condition lúc Add Coupon)
            if (session('cart_ma_id')) {
                /** @var \App\Models\MaGiamGia $maCheck */
                $maCheck = \App\Models\MaGiamGia::where('id', session('cart_ma_id'))->lockForUpdate()->first();
                if ($maCheck) {
                    if ($maCheck->so_luong !== null && $maCheck->da_dung >= $maCheck->so_luong) {
                        throw new \Exception('Mã giảm giá đã hết lượt sử dụng trong tích tắc. Vui lòng thanh toán cập nhật giá mới!');
                    }
                    if (Auth::check()) {
                        $userUsed = \App\Models\DonHang::where('user_id', Auth::id())
                            ->where('ma_giam_gia_id', $maCheck->id)
                            ->where('trang_thai', '!=', 'huy')->exists();
                        if ($userUsed) {
                            throw new \Exception('Tài khoản này đã dùng mã giảm giá. Không thể áp dụng tiếp!');
                        }
                    }
                    // Tăng lượt dùng (Trừ 1 suất vào Database)
                    $maCheck->da_dung += 1;
                    $maCheck->save();
                }
            }

            // Tạo đơn hàng
            $donHang = DonHang::create([
                'user_id'           => Auth::id(),
                'ho_ten'            => $request->ho_ten,
                'so_dien_thoai'     => $request->so_dien_thoai,
                'dia_chi_giao'      => $request->dia_chi,
                'ghi_chu'           => $request->ghi_chu,
                'phuong_thuc_tt'    => $request->phuong_thuc_tt,
                'tong_tien'         => $subtotal,
                'giam_gia'          => $discount,
                'phi_van_chuyen'    => $phi_ship,
                'thanh_toan'        => $tong_tien,
                'trang_thai'        => $request->phuong_thuc_tt === 'cod' ? 'cho_xac_nhan' : 'cho_thanh_toan',
                'ma_giam_gia_id'    => session('cart_ma_id'),
            ]);

            // Tạo chi tiết đơn hàng + trừ tồn kho
            foreach ($cart as $item) {
                // KHOÁ DÒNG SÁCH ĐỂ NGĂN RACECONDITION (Pessimistic Locking)
                $sachDb = Sach::where('id', $item['sach_id'])->lockForUpdate()->first();

                if (!$sachDb || $sachDb->so_luong_ton < $item['so_luong']) {
                    throw new \Exception('Sản phẩm "' . $item['tieu_de'] . '" đã vượt số lượng tồn kho. Vui lòng kiểm tra lại giỏ hàng.');
                }

                DonHangChiTiet::create([
                    'don_hang_id' => $donHang->id,
                    'sach_id'     => $item['sach_id'],
                    'so_luong'    => $item['so_luong'],
                    'don_gia'     => $item['gia_ban'],
                    'thanh_tien'  => $item['gia_ban'] * $item['so_luong'],
                ]);

                // Trừ tồn kho an toàn
                $sachDb->decrement('so_luong_ton', $item['so_luong']);
            }

            DB::commit();

            // Xóa những sản phẩm đã thanh toán khỏi session cart
            $currentCart = session('cart', []);
            foreach ($checkoutItems as $key) {
                unset($currentCart[$key]);
            }
            session(['cart' => $currentCart]);
            
            // Xóa session checkout và discount
            session()->forget(['checkout_items', 'cart_discount', 'cart_coupon', 'cart_ma_id']);

            if ($request->phuong_thuc_tt === 'payos') {
                $payOS = new \PayOS\PayOS(
                    config('services.payos.client_id'),
                    config('services.payos.api_key'),
                    config('services.payos.checksum_key')
                );

                $orderCode = intval($donHang->id . rand(1000, 9999));
                $donHang->update(['payos_order_code' => $orderCode]);

                $data = [
                    "orderCode" => $orderCode,
                    "amount" => $tong_tien,
                    "description" => substr("DH" . $donHang->id, 0, 25),
                    "returnUrl" => route('payos.return'),
                    "cancelUrl" => route('payos.return') // PayOS uses same return url but with status=CANCELLED
                ];

                try {
                    $response = $payOS->createPaymentLink($data);
                    return redirect($response['checkoutUrl']);
                } catch (\Throwable $th) {
                    Log::error('Lỗi khi tạo thanh toán PayOS: ' . $th->getMessage());
                    $lock->release(); // Giải phóng lock nếu có lỗi xảy ra
                    return back()->with('error', 'Lỗi tạo thanh toán PayOS. Vui lòng thử lại sau.');
                }
            }

            return redirect()->route('order.success', ['order_id' => $donHang->id])
                ->with('success', 'Đặt hàng thành công! Mã đơn hàng #' . $donHang->id);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($lock)) {
                $lock->release();
            }
            return back()->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    // =========================================================================
    //  Trang đặt hàng thành công
    // =========================================================================

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return redirect()->route('home');
        }

        $donHang = DonHang::with(['chiTiets.sach', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($orderId);

        // Thông tin Casso cho chuyển khoản
        $cassoInfo = null;
        if ($donHang->phuong_thuc_tt !== 'cod') {
            $cassoInfo = [
                'bank_name'    => config('payment.casso_bank_name', 'MB Bank'),
                'account_no'   => config('payment.casso_account_no', ''),
                'account_name' => config('payment.casso_account_name', 'Modtra Books'),
                'amount'       => $donHang->thanh_toan,
                'description'  => 'MODTRA' . str_pad($donHang->id, 6, '0', STR_PAD_LEFT),
            ];
        }

        return view('pages.order-success', compact('donHang', 'cassoInfo'));
    }

    // =========================================================================
    //  Theo dõi đơn hàng (Yêu cầu đăng nhập)
    // =========================================================================
    public function tracking($id)
    {
        $donHang = DonHang::with(['chiTiets.sach.tacGia'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pages.order-tracking', compact('donHang'));
    }

    // =========================================================================
    //  Theo dõi đơn hàng (Dành cho khách - Qua SĐT + Mã ĐH)
    // =========================================================================
    public function showTrackingSearch()
    {
        return view('pages.order-tracking-search');
    }

    public function findOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'phone'    => 'required|string',
        ]);

        // Xử lý mã đơn (bỏ prefix MB nếu có)
        $id = preg_replace('/[^0-9]/', '', $request->order_id);

        $donHang = DonHang::where('id', $id)
            ->where('so_dien_thoai', $request->phone)
            ->first();

        if (!$donHang) {
            return back()->with('error', 'Không tìm thấy đơn hàng phù hợp với thông tin đã nhập.');
        }

        return redirect()->route('tracking.result', ['id' => $donHang->id, 'phone' => $donHang->so_dien_thoai]);
    }

    public function trackingResult($id, Request $request)
    {
        $donHang = DonHang::with(['chiTiets.sach.tacGia'])
            ->where('id', $id)
            ->where('so_dien_thoai', $request->phone)
            ->firstOrFail();

        return view('pages.order-tracking', compact('donHang'));
    }
}
