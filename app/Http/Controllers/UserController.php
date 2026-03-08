<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Danh sách người dùng với tìm kiếm & lọc theo vai trò.
     */
    public function index(Request $request)
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

        return view('admin.users', compact('users', 'roles', 'tongTatCa', 'tongAdmin', 'tongUser'));
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
