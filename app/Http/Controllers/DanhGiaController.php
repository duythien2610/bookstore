<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\DonHangChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DanhGiaController extends Controller
{
    /**
     * Gửi đánh giá mới cho sách.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sach_id'      => 'required|exists:sach,id',
            'so_sao'       => 'required|integer|min:1|max:5',
            'tieu_de'      => 'nullable|string|max:200',
            'binh_luan'    => 'nullable|string|max:2000',
            'hinh_anh'     => 'nullable|array|max:3',
            'hinh_anh.*'   => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'sach_id.required'  => 'Sách không hợp lệ.',
            'so_sao.required'   => 'Vui lòng chọn số sao.',
            'so_sao.min'        => 'Số sao tối thiểu là 1.',
            'so_sao.max'        => 'Số sao tối đa là 5.',
            'tieu_de.max'       => 'Tiêu đề không quá 200 ký tự.',
            'binh_luan.max'     => 'Bình luận không quá 2000 ký tự.',
            'hinh_anh.max'      => 'Chỉ được tải lên tối đa 3 ảnh.',
            'hinh_anh.*.image'  => 'File phải là hình ảnh.',
            'hinh_anh.*.mimes'  => 'Ảnh phải có định dạng jpeg, png hoặc jpg.',
            'hinh_anh.*.max'    => 'Mỗi ảnh không được vượt quá 2MB.',
        ]);

        $userId = Auth::id();
        $sachId = $request->sach_id;

        // Kiểm tra đã đánh giá rồi chưa
        if (DanhGia::where('user_id', $userId)->where('sach_id', $sachId)->exists()) {
            return back()->with('error', 'Bạn đã đánh giá cuốn sách này rồi!');
        }

        // Kiểm tra người dùng đã mua và nhận sách này chưa (đơn trạng thái da_giao)
        $daMua = DonHangChiTiet::whereHas('donHang', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('trang_thai', 'da_giao');
        })->where('sach_id', $sachId)->exists();

        if (!$daMua) {
            return back()->with('error', 'Bạn cần mua và nhận sách này mới có thể đánh giá.');
        }

        // Xử lý upload ảnh
        $hinhAnh = [];
        if ($request->hasFile('hinh_anh')) {
            $uploadDir = public_path('uploads/reviews');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            foreach ($request->file('hinh_anh') as $file) {
                $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $hinhAnh[] = $filename;
            }
        }

        // Tạo đánh giá (trang_thai = 1 = hiển thị)
        DanhGia::create([
            'user_id'    => $userId,
            'sach_id'    => $sachId,
            'so_sao'     => $request->so_sao,
            'tieu_de'    => $request->tieu_de,
            'binh_luan'  => $request->binh_luan,
            'hinh_anh'   => !empty($hinhAnh) ? $hinhAnh : null,
            'trang_thai' => 1,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá! ⭐');
    }

    /**
     * Xóa đánh giá (chỉ chủ nhân mới được xóa).
     */
    public function destroy($id)
    {
        $danhGia = DanhGia::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $sachId = $danhGia->sach_id;
        $danhGia->delete();

        return redirect()->route('products.show', $sachId)->with('success', 'Đã xóa đánh giá của bạn.');
    }
}
