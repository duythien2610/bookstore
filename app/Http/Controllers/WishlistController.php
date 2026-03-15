<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    private function getWishlist(): array
    {
        return session('wishlist', []);
    }

    private function saveWishlist(array $list): void
    {
        session(['wishlist' => $list]);
        
        if (Auth::check()) {
            $userId = Auth::id();
            \App\Models\Wishlist::where('user_id', $userId)->delete();
            
            $insertData = [];
            foreach (array_keys($list) as $sachId) {
                $insertData[] = [
                    'user_id' => $userId,
                    'sach_id' => $sachId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insertData)) {
                \App\Models\Wishlist::insert($insertData);
            }
        }
    }

    // =========================================================================
    //  Hiển thị danh sách yêu thích
    // =========================================================================

    public function show()
    {
        $ids      = array_keys($this->getWishlist());
        $sachs    = empty($ids) ? collect() : Sach::with('tacGia')->whereIn('id', $ids)->get();

        return view('pages.wishlist', compact('sachs'));
    }

    // =========================================================================
    //  Toggle (thêm/xóa) yêu thích — trả JSON
    // =========================================================================

    public function toggle(Request $request)
    {
        $request->validate(['sach_id' => 'required|exists:sach,id']);

        $list = $this->getWishlist();
        $key  = (string) $request->sach_id;

        if (isset($list[$key])) {
            unset($list[$key]);
            $added = false;
        } else {
            $list[$key] = true;
            $added = true;
        }

        $this->saveWishlist($list);

        return response()->json([
            'success' => true,
            'added'   => $added,
            'count'   => count($list),
            'message' => $added ? 'Đã thêm vào yêu thích!' : 'Đã xóa khỏi yêu thích.',
        ]);
    }
}
