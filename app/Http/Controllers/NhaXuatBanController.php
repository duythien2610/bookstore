<?php

namespace App\Http\Controllers;

use App\Models\NhaXuatBan;
use Illuminate\Http\Request;

class NhaXuatBanController extends Controller
{
    /**
     * Hiển thị form thêm nhà xuất bản.
     */
    public function create()
    {
        return view('admin.add-nha-xuat-ban');
    }

    /**
     * Lưu nhà xuất bản mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_nxb' => 'required|string|max:150|unique:nha_xuat_ban,ten_nxb',
        ], [
            'ten_nxb.required' => 'Tên nhà xuất bản là bắt buộc.',
            'ten_nxb.max'      => 'Tên nhà xuất bản không quá 150 ký tự.',
            'ten_nxb.unique'   => 'Nhà xuất bản này đã tồn tại.',
        ]);

        NhaXuatBan::create($validated);

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Thêm nhà xuất bản "' . $validated['ten_nxb'] . '" thành công!');
    }

    /**
     * Cập nhật nhà xuất bản.
     */
    public function update(Request $request, $id)
    {
        $nxb = NhaXuatBan::findOrFail($id);

        $validated = $request->validate([
            'ten_nxb' => 'required|string|max:150|unique:nha_xuat_ban,ten_nxb,' . $id,
        ], [
            'ten_nxb.required' => 'Tên nhà xuất bản là bắt buộc.',
            'ten_nxb.max'      => 'Tên nhà xuất bản không quá 150 ký tự.',
            'ten_nxb.unique'   => 'Nhà xuất bản này đã tồn tại.',
        ]);

        $nxb->update($validated);

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Cập nhật nhà xuất bản "' . $validated['ten_nxb'] . '" thành công!');
    }

    /**
     * Xóa nhà xuất bản.
     */
    public function destroy($id)
    {
        $nxb = NhaXuatBan::withCount('sachs')->findOrFail($id);

        if ($nxb->sachs_count > 0) {
            return back()->with('error', 'Không thể xóa NXB "' . $nxb->ten_nxb . '" vì vẫn còn ' . $nxb->sachs_count . ' sách liên kết.');
        }

        $ten = $nxb->ten_nxb;
        $nxb->delete();

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Đã xóa nhà xuất bản "' . $ten . '" thành công!');
    }
}
