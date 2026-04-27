<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Admin list of product reviews.
     *
     * Supports a `search` parameter matching against:
     *   - The book's `tieu_de`
     *   - The reviewer's `ho_ten` or `email`
     *   - The review's `tieu_de` or `binh_luan`
     *
     * Also supports a `stars` filter (1..5) via the extra select.
     *
     * AJAX requests receive a JSON payload with a pre-rendered rows
     * partial so the shared js-admin-search helper can swap the tbody.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $stars  = $request->input('stars');

        $query = DanhGia::with(['user', 'sach'])->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                  ->orWhere('binh_luan', 'like', "%{$search}%")
                  ->orWhereHas('sach', function ($b) use ($search) {
                      $b->where('tieu_de', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('ho_ten', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($stars !== null && $stars !== '' && in_array((int) $stars, [1, 2, 3, 4, 5], true)) {
            $query->where('so_sao', (int) $stars);
        }

        $reviews = $query->paginate(20)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $reviews->total(),
                'html'    => view('admin._partials.reviews_rows', compact('reviews'))->render(),
            ]);
        }

        $stats = [
            'total'   => DanhGia::count(),
            'visible' => DanhGia::where('trang_thai', 1)->count(),
            'hidden'  => DanhGia::where('trang_thai', 0)->count(),
            'avg'     => round(DanhGia::avg('so_sao') ?? 0, 2),
        ];

        return view('admin.reviews', compact('reviews', 'stats'));
    }

    /**
     * Toggle a review's visibility (trang_thai: 1 = shown, 0 = hidden).
     */
    public function toggle($id)
    {
        $review = DanhGia::findOrFail($id);
        $review->update(['trang_thai' => $review->trang_thai ? 0 : 1]);

        return back()->with('success',
            $review->trang_thai ? 'Đã hiển thị đánh giá.' : 'Đã ẩn đánh giá.'
        );
    }

    /**
     * Permanently delete a review.
     */
    public function destroy($id)
    {
        DanhGia::findOrFail($id)->delete();
        return back()->with('success', 'Đã xoá đánh giá.');
    }
}
