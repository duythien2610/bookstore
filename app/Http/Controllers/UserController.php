<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Hiển thị trang hồ sơ của user đang đăng nhập.
     */
    public function show()
    {
        $user = Auth::user();
        return view('pages.profile', compact('user'));
    }

    /**
     * Danh sách đơn hàng của tôi.
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = \App\Models\DonHang::where('user_id', $user->id)
            ->with(['chiTiets.sach'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // IDs sách mà user đã đánh giá (để biết nút nào disabled)
        $reviewedSachIds = \App\Models\DanhGia::where('user_id', $user->id)
            ->pluck('sach_id')
            ->toArray();

        return view('pages.orders', compact('user', 'orders', 'reviewedSachIds'));
    }

    /**
     * Cập nhật thông tin cá nhân.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ho_ten'        => 'required|string|max:100',
            'so_dien_thoai' => 'nullable|string|max:15',
            'ngay_sinh'     => 'nullable|date',
            'gioi_tinh'     => 'nullable|in:male,female,other',
            // dia_chi đã được ghép bởi JavaScript (format: tenDuong|xa|huyen|tinh)
            'dia_chi'       => 'nullable|string|max:500',
        ]);

        $user->update([
            'ho_ten'        => $request->ho_ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'ngay_sinh'     => $request->ngay_sinh,
            'gioi_tinh'     => $request->gioi_tinh,
            // Nếu chỉ 4 pipe rỗng "|||" thì lưu null
            'dia_chi'       => ($request->dia_chi && trim($request->dia_chi, '|') !== '') ? $request->dia_chi : null,
        ]);

        return redirect()->route('profile')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Danh sách người dùng với tìm kiếm & lọc theo vai trò.
     */
    public function index(Request $request, $pageTitle = 'Quản lý người dùng')
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%")
                  ->orWhere('so_dien_thoai', 'like', "%$keyword%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users  = $query->orderByDesc('created_at')->get();
        $roles  = Role::all();

        $tongTatCa  = User::count();
        $tongAdmin  = User::where('role_id', 1)->count();
        $tongUser   = User::where('role_id', 2)->count();

        return view('admin.users', compact('users', 'roles', 'tongTatCa', 'tongAdmin', 'tongUser', 'pageTitle'));
    }

    /**
     * Tham chiếu danh sách Khách hàng
     */
    public function customers(Request $request)
    {
        $request->merge(['role_id' => 2]);
        return $this->index($request, 'Khách hàng');
    }

    /**
     * Tham chiếu danh sách Quản trị viên
     */
    public function admins(Request $request)
    {
        $request->merge(['role_id' => 1]);
        return $this->index($request, 'Quản trị viên');
    }

    /**
     * Đổi vai trò của user (admin ↔ user).
     */
    public function updateRole(Request $request, User $user)
    {
        // Không cho phép admin tự đổi chính mình
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể thay đổi vai trò của chính mình.');
        }

        $request->validate(['role_id' => 'required|exists:roles,id']);

        $user->update(['role_id' => $request->role_id]);

        return back()->with('success', 'Đã cập nhật vai trò của "' . $user->ho_ten . '" thành công!');
    }

    /**
     * Xóa người dùng.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể xóa chính mình.');
        }

        $name = $user->ho_ten;
        $user->delete();

        return back()->with('success', 'Đã xóa người dùng "' . $name . '" thành công!');
    }
}
