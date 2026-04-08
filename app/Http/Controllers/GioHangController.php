<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GioHangController extends Controller
{
    public function index()
    {
        $gioHang = GioHang::where('user_id', auth()->id())
            ->where('trang_thai', 'active')
            ->first();
        
        $items = $gioHang ? $gioHang->chiTiets()->with('sach')->get() : collect();
        $discount = session('cart_discount', 0);
        $couponCode = session('cart_coupon', null);
        
        return view('pages.cart', compact('items', 'gioHang', 'discount', 'couponCode'));
    }

    public function add(Request $request)
    {
        $sachId = $request->input('sach_id');
        $soLuong = $request->input('so_luong' ?: 1, 1);
        $sach = Sach::findOrFail($sachId);

        if (!$sach->conHang()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Sách này hiện đang hết hàng!'], 400);
            }
            return redirect()->back()->with('error', 'Sách này hiện đang hết hàng!');
        }

        // Lấy hoặc tạo giỏ hàng
        $gioHang = GioHang::firstOrCreate(
            ['user_id' => auth()->id(), 'trang_thai' => 'active']
        );

        // Kiểm tra xem đã có sách này trong giỏ chưa
        $chiTiet = $gioHang->chiTiets()->where('sach_id', $sachId)->first();

        if ($chiTiet) {
            $chiTiet->so_luong += $soLuong;
            $chiTiet->thanh_tien = $chiTiet->so_luong * $chiTiet->don_gia;
            $chiTiet->save();
        } else {
            $gioHang->chiTiets()->create([
                'sach_id' => $sachId,
                'so_luong' => $soLuong,
                'don_gia' => $sach->gia_ban,
                'thanh_tien' => $soLuong * $sach->gia_ban
            ]);
        }

        // Cập nhật tổng tiền giỏ hàng
        $gioHang->tong_tien = $gioHang->chiTiets()->sum('thanh_tien');
        $gioHang->save();

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Đã thêm vào giỏ hàng', 
                'cart_count' => $gioHang->chiTiets()->count(),
                'total_html' => number_format($gioHang->tong_tien, 0, ',', '.') . 'đ'
            ]);
        }

        return redirect()->route('cart')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request, $id)
    {
        $chiTiet = GioHangChiTiet::where('id', $id)->firstOrFail();
        $soLuong = $request->input('so_luong');

        if ($soLuong <= 0) {
            $chiTiet->delete();
        } else {
            $chiTiet->so_luong = $soLuong;
            $chiTiet->thanh_tien = $soLuong * $chiTiet->don_gia;
            $chiTiet->save();
        }

        $gioHang = $chiTiet->gioHang;
        $gioHang->tong_tien = $gioHang->chiTiets()->sum('thanh_tien');
        $gioHang->save();

        if ($request->ajax()) {
            return response()->json([
                'thanh_tien' => number_format($chiTiet->thanh_tien, 0, ',', '.') . 'đ',
                'tong_tien' => number_format($gioHang->tong_tien, 0, ',', '.') . 'đ'
            ]);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $chiTiet = GioHangChiTiet::where('id', $id)->firstOrFail();
        $gioHang = $chiTiet->gioHang;
        $chiTiet->delete();

        $gioHang->tong_tien = $gioHang->chiTiets()->sum('thanh_tien');
        $gioHang->save();

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function showCheckout()
    {
        $gioHang = GioHang::where('user_id', auth()->id())
            ->where('trang_thai', 'active')
            ->firstOrFail();
        
        $items = $gioHang->chiTiets()->with('sach')->get();
        if ($items->isEmpty()) return redirect()->route('cart');

        return view('pages.checkout', compact('items', 'gioHang'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string',
            'dien_thoai' => 'required',
            'dia_chi_giao' => 'required',
            'phuong_thuc_tt' => 'required|in:cod,vnpay'
        ]);

        $donHangId = DB::transaction(function() use ($request) {
            $gioHang = GioHang::where('user_id', auth()->id())
                ->where('trang_thai', 'active')
                ->firstOrFail();
            
            // Tạo đơn hàng
            $donHang = DonHang::create([
                'user_id' => auth()->id(),
                'ngay_dat' => now(),
                'trang_thai' => 'cho_xac_nhan',
                'tong_tien' => $gioHang->tong_tien,
                'dia_chi_giao' => $request->dia_chi_giao,
                'phuong_thuc_tt' => $request->phuong_thuc_tt,
                'trang_thai_tt' => 'chua_thanh_toan',
                'ghi_chu' => $request->ghi_chu
            ]);

            // Sao chép các mặt hàng từ giỏ hàng sang đơn hàng
            foreach ($gioHang->chiTiets as $item) {
                DonHangChiTiet::create([
                    'don_hang_id' => $donHang->id,
                    'sach_id' => $item->sach_id,
                    'so_luong' => $item->so_luong,
                    'don_gia' => $item->don_gia,
                    'thanh_tien' => $item->thanh_tien
                ]);
            }

            // Đóng giỏ hàng
            $gioHang->trang_thai = 'completed';
            $gioHang->save();
            
            return $donHang->id;
        });

        return redirect()->route('order.success')->with('success', 'Đặt hàng thành công!');
    }
}
