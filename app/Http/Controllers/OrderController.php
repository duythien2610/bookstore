<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng (có filter theo trạng thái).
     */
    public function index(Request $request)
    {
        $query = DonHang::with('user')->orderByDesc('created_at');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Tìm kiếm theo ID đơn hàng (Mã đơn), Tên khách, SĐT
        if ($request->filled('search')) {
            $s = $request->search;
            // Xử lý nếu người dùng nhập mã có tiền tố MB (ví dụ MB000108 -> ID 108)
            $cleanId = preg_replace('/[^0-9]/', '', $s);
            
            $query->where(function ($q) use ($s, $cleanId) {
                if (!empty($cleanId)) {
                    $q->where('id', $cleanId);
                }
                $q->orWhere('id', 'like', "%{$s}%")
                  ->orWhere('ho_ten', 'like', "%{$s}%")
                  ->orWhere('so_dien_thoai', 'like', "%{$s}%");
            });
        }

        // Lọc theo ngày đặt
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $donHangs     = $query->paginate(15);
        $donHangs->withQueryString();
        $statusCounts = DonHang::selectRaw('trang_thai, count(*) as total')
                                ->groupBy('trang_thai')
                                ->pluck('total', 'trang_thai')
                                ->toArray();
        $tongTatCa = DonHang::count();

        return view('admin.orders', compact('donHangs', 'statusCounts', 'tongTatCa'));
    }

    /**
     * Chi tiết đơn hàng + cập nhật trạng thái.
     */
    public function show($id)
    {
        $donHang = DonHang::with(['user', 'chiTiets.sach.tacGia', 'maGiamGia'])
                          ->findOrFail($id);
        return view('admin.order-detail', compact('donHang'));
    }

    /**
     * Cập nhật trạng thái đơn hàng.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:cho_thanh_toan,cho_xac_nhan,dang_xu_ly,dang_giao,da_giao,huy',
        ]);

        $donHang = DonHang::findOrFail($id);
        $donHang->update(['trang_thai' => $request->trang_thai]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng #' . str_pad($id, 6, '0', STR_PAD_LEFT) . '!');
    }
}
