<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\MaGiamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CartController extends Controller
{
    // =========================================================================
    //  Helpers — Session cart
    // =========================================================================

    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);

        if (Auth::check()) {
            $user = Auth::user();
            $gioHang = \App\Models\GioHang::firstOrCreate(['user_id' => $user->id]);
            $gioHang->chiTiets()->delete();
            $gioHang->tong_tien = $this->getCartTotal($cart);
            $gioHang->save();
            foreach ($cart as $item) {
                $gioHang->chiTiets()->create([
                    'sach_id'    => $item['sach_id'],
                    'so_luong'   => $item['so_luong'],
                    'don_gia'    => $item['gia_ban'],
                    'thanh_tien' => $item['gia_ban'] * $item['so_luong'],
                ]);
            }
        }
    }

    public function getCartTotal(array $cart): int
    {
        return array_sum(array_map(fn($item) => $item['gia_ban'] * $item['so_luong'], $cart));
    }

    // =========================================================================
    //  Hiển thị giỏ hàng
    // =========================================================================

    public function show()
    {
        $cart    = $this->getCart();
        $total   = $this->getCartTotal($cart);
        $discount = session('cart_discount', 0);
        $couponCode = session('cart_coupon', null);

        return view('pages.cart', compact('cart', 'total', 'discount', 'couponCode'));
    }

    // =========================================================================
    //  Thêm vào giỏ
    // =========================================================================

    public function add(Request $request)
    {
        $request->validate([
            'sach_id'  => 'required|exists:sach,id',
            'so_luong' => 'required|integer|min:1|max:50',
        ]);

        $sach = Sach::findOrFail($request->sach_id);

        if ($sach->so_luong_ton < 1) {
            return back()->with('error', 'Sách này đã hết hàng!');
        }

        $cart = $this->getCart();
        $key  = (string) $sach->id;

        if (isset($cart[$key])) {
            $newQty = $cart[$key]['so_luong'] + $request->so_luong;
            $cart[$key]['so_luong'] = min($newQty, $sach->so_luong_ton);
        } else {
            $cart[$key] = [
                'sach_id'    => $sach->id,
                'tieu_de'    => $sach->tieu_de,
                'ten_tac_gia'=> $sach->tacGia->ten_tac_gia ?? 'Không rõ',
                'gia_ban'    => (int) $sach->gia_ban,
                'gia_goc'    => (int) ($sach->gia_goc ?? 0),
                'anh_bia'    => $sach->file_anh_bia ?? $sach->link_anh_bia,
                'so_luong'   => (int) $request->so_luong,
                'ton_kho'    => (int) $sach->so_luong_ton,
            ];
        }

        $this->saveCart($cart);

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'cart_count'=> count($cart),
                'message'   => 'Đã thêm "' . $sach->tieu_de . '" vào giỏ hàng!',
            ]);
        }

        return back()->with('success', 'Đã thêm "' . $sach->tieu_de . '" vào giỏ hàng!');
    }

    // =========================================================================
    //  Cập nhật số lượng
    // =========================================================================

    public function update(Request $request, $id)
    {
        $request->validate(['so_luong' => 'required|integer|min:1|max:50']);

        $cart = $this->getCart();
        $key  = (string) $id;

        if (isset($cart[$key])) {
            $sach = Sach::find($id);
            $maxQty = $sach ? $sach->so_luong_ton : 50;
            $cart[$key]['so_luong'] = min((int)$request->so_luong, $maxQty);
            $this->saveCart($cart);
        }

        $total    = $this->getCartTotal($cart);
        $discount = session('cart_discount', 0);

        if ($request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'item_total'   => isset($cart[$key]) ? $cart[$key]['gia_ban'] * $cart[$key]['so_luong'] : 0,
                'cart_total'   => $total,
                'final_total'  => max(0, $total - $discount),
                'cart_count'   => count($cart),
            ]);
        }

        return back();
    }

    // =========================================================================
    //  Xóa item
    // =========================================================================

    public function remove(Request $request, $id)
    {
        $cart = $this->getCart();
        unset($cart[(string)$id]);
        $this->saveCart($cart);

        $total    = $this->getCartTotal($cart);
        $discount = session('cart_discount', 0);

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'cart_total' => $total,
                'final_total'=> max(0, $total - $discount),
                'cart_count' => count($cart),
                'is_empty'   => empty($cart),
            ]);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    // =========================================================================
    //  Xóa toàn bộ giỏ
    // =========================================================================

    public function clear()
    {
        session()->forget(['cart', 'cart_discount', 'cart_coupon']);
        if (Auth::check()) {
            \App\Models\GioHang::where('user_id', Auth::id())->delete();
        }
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }

    // =========================================================================
    //  Áp mã giảm giá
    // =========================================================================

    public function applyCoupon(Request $request)
    {
        // Hỗ trợ xóa mã giảm giá
        if ($request->input('remove')) {
            session()->forget(['cart_discount', 'cart_coupon', 'cart_ma_id']);
            return response()->json(['success' => true, 'message' => 'Đã xóa mã giảm giá.']);
        }

        $request->validate(['coupon' => 'required|string']);

        $ma = MaGiamGia::where('ma_code', $request->coupon)
            ->where('trang_thai', 1)
            ->where(function($q) {
                $q->whereNull('ngay_het_han')
                  ->orWhere('ngay_het_han', '>=', now());
            })
            ->first();

        if (!$ma) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.']);
        }

        // KIỂM TRA SỐ LƯỢNG MÃ CÒN LẠI (NGỪA VƯỢT QUÁ SO LUONG)
        if ($ma->so_luong !== null && $ma->da_dung >= $ma->so_luong) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá này đã hết lượt sử dụng.']);
        }

        // KIỂM TRA MỖI KHÁCH HÀNG CHỈ ĐƯỢC: 1 LẦN/1 MÃ GIẢM GIÁ (CHỐNG COUPON STACKING)
        if (Auth::check()) {
            $userUsed = \App\Models\DonHang::where('user_id', Auth::id())
                ->where('ma_giam_gia_id', $ma->id)
                ->where('trang_thai', '!=', 'huy') // Nếu hủy đơn cũ thì cho phép dùng lại
                ->exists();
            if ($userUsed) {
                return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này rồi. Mỗi tài khoản chỉ được dùng 1 lần.']);
            }
        }

        // KIỂM TRA ĐIỀU KIỆN TÀI KHOẢN
        if ($ma->dieu_kien_tai_khoan === 'new') {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Mã này chỉ dành cho tài khoản mới. Vui lòng đăng nhập.']);
            }
            $daysSinceRegister = Auth::user()->created_at->diffInDays(now());
            if ($daysSinceRegister > 30) {
                return response()->json(['success' => false, 'message' => 'Mã này chỉ dành cho tài khoản đăng ký trong 30 ngày gần đây.']);
            }
        }
        if ($ma->dieu_kien_tai_khoan === 'verified') {
            if (!Auth::check() || !Auth::user()->email_verified_at) {
                return response()->json(['success' => false, 'message' => 'Mã này yêu cầu tài khoản đã xác thực email.']);
            }
        }

        // Lấy tổng tiền từ giỏ hàng (DB nếu đã login, session nếu chưa - nhưng app hiện dùng DB cho /cart)
        $total = 0;
        if (Auth::check()) {
            $total = \App\Models\GioHang::where('user_id', Auth::id())
                ->where('trang_thai', 'active')
                ->first()?->chiTiets()->sum('thanh_tien') ?? 0;
        } else {
            $cart = $this->getCart();
            $total = $this->getCartTotal($cart);
        }

        if ($total <= 0) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống, không thể áp dụng mã.']);
        }

        // KIỂM TRA GIÁ TRỊ ĐƠN HÀNG TỐI THIỂU
        if ($ma->don_hang_toi_thieu && $total < $ma->don_hang_toi_thieu) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng phải đạt tối thiểu ' . number_format((float)$ma->don_hang_toi_thieu, 0, ',', '.') . 'đ để dùng mã này.',
            ]);
        }

        $discount = $ma->loai === 'percent'
            ? (int)($total * $ma->gia_tri / 100)
            : (int)$ma->gia_tri;
        $discount = min($discount, $total);

        session([
            'cart_discount' => $discount,
            'cart_coupon'   => $request->coupon,
            'cart_ma_id'    => $ma->id,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Áp dụng mã giảm giá thành công! Giảm ' . number_format($discount, 0, ',', '.') . 'đ',
            'discount'   => $discount,
            'final_total'=> max(0, $total - $discount),
        ]);
    }
}
