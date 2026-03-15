<?php

namespace App\Http\Controllers;

use App\Models\MaGiamGia;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = MaGiamGia::orderByDesc('created_at')->get();
        return view('admin.coupons', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_code'      => 'required|string|max:50|unique:ma_giam_gia,ma_code',
            'loai'         => 'required|in:percent,fixed',
            'gia_tri'      => 'required|numeric|min:0',
            'ngay_het_han' => 'nullable|date|after_or_equal:today',
            'so_luong'     => 'nullable|integer|min:1',
        ], [
            'ma_code.required'   => 'Mã coupon là bắt buộc.',
            'ma_code.unique'     => 'Mã coupon này đã tồn tại.',
            'loai.required'      => 'Loại giảm giá là bắt buộc.',
            'gia_tri.required'   => 'Giá trị giảm là bắt buộc.',
            'ngay_het_han.after_or_equal' => 'Hạn dùng phải từ hôm nay trở đi.',
        ]);

        MaGiamGia::create([
            'ma_code'      => strtoupper(trim($request->ma_code)),
            'loai'         => $request->loai,
            'gia_tri'      => $request->gia_tri,
            'ngay_het_han' => $request->ngay_het_han,
            'so_luong'     => $request->so_luong,
            'da_dung'      => 0,
            'trang_thai'   => 1,
        ]);

        return back()->with('success', 'Tạo mã giảm giá "'  . strtoupper($request->ma_code) . '" thành công!');
    }

    public function toggleStatus($id)
    {
        $ma = MaGiamGia::findOrFail($id);
        $ma->update(['trang_thai' => $ma->trang_thai ? 0 : 1]);
        return back()->with('success', 'Đã cập nhật trạng thái mã giảm giá.');
    }

    public function destroy($id)
    {
        MaGiamGia::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa mã giảm giá.');
    }
}
