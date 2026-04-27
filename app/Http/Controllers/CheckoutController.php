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
use App\Services\ShippingService;
use App\Jobs\SendOrderConfirmationEmail;

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
        $user    = Auth::user();
        $gioHang = GioHang::where('user_id', auth()->id())->where('trang_thai', 'active')->first();
        $items   = $gioHang ? $gioHang->chiTiets()->with('sach')->get() : collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng đang trống!');
        }

        $cart = [];
        $subtotal = 0;
        foreach ($items as $item) {
            $cart[] = [
                'sach_id'      => $item->sach_id,
                'tieu_de'      => $item->sach->tieu_de,
                'so_luong'     => $item->so_luong,
                'gia_ban'      => $item->don_gia,
                'thanh_tien'   => $item->thanh_tien,
                'link_anh_bia' => $item->sach->link_anh_bia,
                'file_anh_bia' => $item->sach->file_anh_bia
            ];
            $subtotal += $item->thanh_tien;
        }

        $discount = session('cart_discount', 0);

        /** @var \App\Models\User $user */
        $userAddresses = $user ? $user->addresses()->orderBy('is_default', 'desc')->get() : collect();

        // Lấy địa chỉ mặc định để tính phí ship và pre-fill
        $defaultAddress = $userAddresses->first();
        
        // Nếu chưa có sổ địa chỉ nhưng có địa chỉ trong thông tin User thì tách ra
        if (!$defaultAddress && $user && $user->dia_chi) {
            // UserController lưu địa chỉ theo chuẩn "tenDuong|xa|huyen|tinh" hoặc dùng dấu phẩy. Ta phân dải ưu tiên dấu '|' trước.
            $delimiter = str_contains($user->dia_chi, '|') ? '|' : ',';
            $parts = array_map('trim', explode($delimiter, $user->dia_chi));
            $count = count($parts);
            
            $defaultAddress = new \stdClass();
            if ($count >= 3) {
                $defaultAddress->tinh_thanh_pho = $parts[$count - 1];
                $defaultAddress->quan_huyen     = $parts[$count - 2];
                $defaultAddress->phuong_xa      = $parts[$count - 3];
                // Lấy phần đầu làm tên đường, nếu có (chỗ này có thể trống nếu tenDuong để trống, lúc này count vẫn là 4 nếu có 3 dấu |)
                $defaultAddress->dia_chi        = $count > 3 ? implode(', ', array_slice($parts, 0, $count - 3)) : '';
            } else {
                $defaultAddress->tinh_thanh_pho = '';
                $defaultAddress->quan_huyen     = '';
                $defaultAddress->phuong_xa      = '';
                $defaultAddress->dia_chi        = $user->dia_chi;
            }
            $defaultAddress->ho_ten = $user->ho_ten;
            $defaultAddress->so_dien_thoai = $user->so_dien_thoai;
        }

        $defaultCity = $defaultAddress ? $defaultAddress->tinh_thanh_pho : '';
        $shippingService = new ShippingService();
        $phi_ship = $shippingService->calculateFee($defaultCity, $subtotal);

        $total = max(0, $subtotal - $discount) + $phi_ship;

        // Thông tin Casso QR thanh toán
        $cassoInfo = [
            'bank_name'    => config('payment.casso_bank_name', 'MB Bank'),
            'account_no'   => config('payment.casso_account_no', ''),
            'account_name' => config('payment.casso_account_name', 'Bookverse'),
        ];

        return view('pages.checkout', compact('cart', 'subtotal', 'discount', 'phi_ship', 'total', 'user', 'cassoInfo', 'userAddresses', 'defaultAddress'));
    }

    // =========================================================================
    //  Xử lý đặt hàng
    // =========================================================================

    public function store(Request $request)
    {
        $gioHang = GioHang::where('user_id', auth()->id())->where('trang_thai', 'active')->first();
        $items = $gioHang ? $gioHang->chiTiets()->with('sach')->get() : collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Không có sản phẩm để thanh toán!');
        }

        $cart = [];
        $subtotal = 0;
        foreach ($items as $item) {
            $cart[] = [
                'sach_id' => $item->sach_id,
                'tieu_de' => $item->sach->tieu_de,
                'so_luong' => $item->so_luong,
                'gia_ban' => $item->don_gia,
                'thanh_tien' => $item->thanh_tien
            ];
            $subtotal += $item->thanh_tien;
        }

        $request->validate([
            'ho_ten'         => 'required|string|max:100',
            'so_dien_thoai'  => 'required|string|max:15',
            'dia_chi'        => 'nullable|string|max:500',
            'city'           => 'nullable|string',
            'district'       => 'nullable|string',
            'ward'           => 'nullable|string',
            'phuong_thuc_tt' => 'required|in:cod,payos',
            'idempotency_key'=> 'required|string',
            'website_url'    => 'nullable|string|max:0', // HONEYPOT
        ], [
            'ho_ten.required'         => 'Vui lòng nhập họ và tên.',
            'so_dien_thoai.required'  => 'Vui lòng nhập số điện thoại.',
            'phuong_thuc_tt.required' => 'Vui lòng chọn phương thức thanh toán.',
            'website_url.max'         => 'Hành vi SPAM BOT. Yêu cầu bị từ chối!',
        ]);

        // CHỐNG ORDER FRAUD (Giới hạn 1 tài khoản tối đa 5 đơn hàng đang chờ xử lý)
        $pendingOrders = DonHang::where('user_id', Auth::id())
            ->whereIn('trang_thai', ['cho_xac_nhan', 'cho_thanh_toan', 'dang_xu_ly'])
            ->count();
            
        if ($pendingOrders >= 5) {
            return back()->with('error', 'Bạn đang có quá nhiều đơn hàng chờ xử lý. Vui lòng thanh toán hoặc chờ xác nhận trước khi đặt thêm!');
        }

        // CHỐNG SPAM THEO SỐ LƯỢNG ĐƠN / NGÀY TỪ 1 USER
        $dailyOrders = DonHang::where('user_id', Auth::id())
            ->whereDate('created_at', \Illuminate\Support\Carbon::today())
            ->count();
            
        if ($dailyOrders >= 20) {
            return back()->with('error', 'Tài khoản của bạn đã đạt giới hạn 20 đơn hàng/ngày. Vui lòng thử lại vào ngày mai!');
        }

        $discount   = session('cart_discount', 0);
        
        $shippingService = new ShippingService();
        $phi_ship   = $shippingService->calculateFee($request->input('city', ''), $subtotal);
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
                        $userUsed = DonHang::where('user_id', Auth::id())
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

            // Ghép địa chỉ đầy đủ từ các thành phần
            // Lấy Tỉnh/Huyện/Xã từ form hoặc fallback từ UserAddress trong DB
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();
            $dbAddress = $authUser ? $authUser->addresses()->orderBy('is_default','desc')->first() : null;

            $city     = $request->input('city')     ?: ($dbAddress->tinh_thanh_pho ?? '');
            $district = $request->input('district') ?: ($dbAddress->quan_huyen     ?? '');
            $ward     = $request->input('ward')     ?: ($dbAddress->phuong_xa      ?? '');
            $diaChiNhap = $request->input('dia_chi') ?: ($dbAddress->dia_chi       ?? '');

            // Ghép địa chỉ đầy đủ
            $addressParts = array_filter([$diaChiNhap, $ward, $district, $city]);
            $fullAddress  = implode(', ', $addressParts) ?: 'Chưa cập nhật';

            // Tạo đơn hàng
            $donHang = DonHang::create([
                'user_id'           => Auth::id(),
                'ho_ten'            => $request->ho_ten,
                'so_dien_thoai'     => $request->so_dien_thoai,
                'dia_chi_giao'      => $fullAddress,
                'ghi_chu'           => $request->ghi_chu,
                'phuong_thuc_tt'    => $request->phuong_thuc_tt,
                'ngay_dat'          => now(),
                'tong_tien'         => $subtotal,
                'giam_gia'          => $discount,
                'phi_van_chuyen'    => $phi_ship,
                'thanh_toan'        => $tong_tien,
                'trang_thai'        => $request->phuong_thuc_tt === 'cod' ? 'cho_xac_nhan' : 'cho_thanh_toan',
                'ma_giam_gia_id'    => session('cart_ma_id'),
            ]);

            // LƯU SCỔ ĐỊA CHỈ NẾU CHỌN
            if ($request->has('save_address') && Auth::check()) {
                \App\Models\UserAddress::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'dia_chi' => $request->dia_chi,
                    ],
                    [
                        'ho_ten' => $request->ho_ten,
                        'so_dien_thoai' => $request->so_dien_thoai,
                        'tinh_thanh_pho' => $request->city ?? null,
                        'quan_huyen' => $request->district ?? null,
                        'phuong_xa' => $request->ward ?? null,
                    ]
                );
            }

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

            // Đóng giỏ hàng sau khi thanh toán xong
            $gioHang->trang_thai = 'completed';
            $gioHang->save();

            // Gửi email xác nhận (Queue) NẾU LÀ THANH TOÁN COD.
            // (Thanh toán PAYOS sẽ chờ Webhook xác nhận thanh toán rồi mới gửi email)
            if ($request->phuong_thuc_tt !== 'payos') {
                try {
                    SendOrderConfirmationEmail::dispatch($donHang);
                } catch (\Exception $e) {
                    Log::error('Lỗi khi gửi email xác nhận: ' . $e->getMessage());
                }
            }

            // Xóa session discount
            session()->forget(['checkout_items', 'cart_discount', 'cart_coupon', 'cart_ma_id']);

            // Giải phóng lock ngay sau khi đơn hàng thành công
            $lock->release();

            if ($request->phuong_thuc_tt === 'payos') {
                error_log("\n========== [DEBUG] ===========\n-> ĐÃ VÀO LUỒNG XỬ LÝ PAYOS!\n=============================\n");

                // Tạo orderCode đảm bảo trong phạm vi INT hợp lệ (max ~2 tỷ)
                // Dùng timestamp + phần đuôi id để tránh trùng
                $orderCode = (int) (time() . substr(str_pad($donHang->id, 4, '0', STR_PAD_LEFT), -4));
                // Đảm bảo không vượt quá PHP_INT_MAX / MySQL INT
                if ($orderCode > 2147483647) {
                    $orderCode = (int) substr((string)$orderCode, -9);
                }

                $donHang->update(['payos_order_code' => $orderCode]);

                try {
                    error_log("-> Sắp gọi hàm createPaymentLink() của thư viện PayOS\n");
                    $payOS = new \PayOS\PayOS(
                        config('services.payos.client_id'),
                        config('services.payos.api_key'),
                        config('services.payos.checksum_key')
                    );

                    // description tối đa 25 ký tự, không dấu
                    $description = 'DH' . $donHang->id . ' Bookverse';
                    $description = substr(preg_replace('/[^a-zA-Z0-9 ]/', '', $description), 0, 25);

                    $paymentData = [
                        'orderCode'   => $orderCode,
                        'amount'      => (int) max(1000, $tong_tien), // PayOS yêu cầu tối thiểu 1000đ
                        'description' => $description,
                        'returnUrl'   => route('payos.return'),
                        'cancelUrl'   => route('payos.return') . '?status=cancel',
                        'buyerName'   => $request->ho_ten,
                        'buyerPhone'  => $request->so_dien_thoai,
                        'expiredAt'   => time() + (15 * 60) // Giới hạn link casso hết hạn sau 15 phút
                    ];

                    Log::info('PayOS createPaymentLink request', $paymentData);

                    $response = $payOS->createPaymentLink($paymentData);

                    Log::info('PayOS createPaymentLink response', $response);

                    if (!isset($response['checkoutUrl'])) {
                        throw new \Exception('PayOS không trả về checkoutUrl. Response: ' . json_encode($response));
                    }

                    return redirect()->away($response['checkoutUrl']);
                } catch (\Throwable $th) {
                    Log::error('Lỗi khi tạo thanh toán PayOS: ' . $th->getMessage(), [
                        'orderCode' => $orderCode,
                        'donHangId' => $donHang->id,
                        'amount'    => $tong_tien,
                    ]);
                    // Huỷ trạng thái đơn để người dùng có thể đặt lại
                    $donHang->update(['trang_thai' => 'huy']);
                    return back()->with('error', 'Lỗi tạo thanh toán PayOS: ' . $th->getMessage());
                }
            }

            return redirect()->route('order.success', ['order_id' => $donHang->id])
                ->with('success', 'Đặt hàng thành công! Mã đơn hàng #' . $donHang->id);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($lock)) {
                $lock->release();
            }
            Log::error('Order creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
                'account_name' => config('payment.casso_account_name', 'Bookverse'),
                'amount'       => $donHang->thanh_toan,
                'description'  => 'BOOKVERSE' . str_pad($donHang->id, 6, '0', STR_PAD_LEFT),
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

    // =========================================================================
    //  API Tính Phí Vận Chuyển Động
    // =========================================================================
    public function calculateShipping(Request $request)
    {
        $city = $request->input('city');
        $gioHang = GioHang::where('user_id', auth()->id())->where('trang_thai', 'active')->first();
        $subtotal = $gioHang ? $gioHang->tong_tien : 0;

        $shippingService = new ShippingService();
        $fee = $shippingService->calculateFee($city, $subtotal);
        
        $discount = session('cart_discount', 0);
        $total = max(0, $subtotal - $discount) + $fee;

        return response()->json([
            'fee' => $fee,
            'fee_formatted' => $fee == 0 ? 'Miễn phí' : number_format($fee, 0, ',', '.') . 'đ',
            'total' => $total,
            'total_formatted' => number_format($total, 0, ',', '.') . 'đ'
        ]);
    }
}
