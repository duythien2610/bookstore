<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    /**
     * Hiển thị form thêm nhà cung cấp.
     */
    public function create()
    {
        return view('admin.add-nha-cung-cap');
    }

    /**
     * Lưu nhà cung cấp mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_ncc' => 'required|string|max:150|unique:nha_cung_cap,ten_ncc',
        ], [
            'ten_ncc.required' => 'Tên nhà cung cấp là bắt buộc.',
            'ten_ncc.max'      => 'Tên nhà cung cấp không quá 150 ký tự.',
            'ten_ncc.unique'   => 'Nhà cung cấp này đã tồn tại.',
        ]);

        NhaCungCap::create($validated);

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Thêm nhà cung cấp "' . $validated['ten_ncc'] . '" thành công!');
    }

    /**
     * Cập nhật nhà cung cấp.
     */
    public function update(Request $request, $id)
    {
        $ncc = NhaCungCap::findOrFail($id);

        $validated = $request->validate([
            'ten_ncc' => 'required|string|max:150|unique:nha_cung_cap,ten_ncc,' . $id,
        ], [
            'ten_ncc.required' => 'Tên nhà cung cấp là bắt buộc.',
            'ten_ncc.max'      => 'Tên nhà cung cấp không quá 150 ký tự.',
            'ten_ncc.unique'   => 'Nhà cung cấp này đã tồn tại.',
        ]);

        $ncc->update($validated);

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Cập nhật nhà cung cấp "' . $validated['ten_ncc'] . '" thành công!');
    }

    /**
     * Xóa nhà cung cấp.
     */
    public function destroy($id)
    {
        $ncc = NhaCungCap::withCount('sachs')->findOrFail($id);

        if ($ncc->sachs_count > 0) {
            return back()->with('error', 'Không thể xóa NCC "' . $ncc->ten_ncc . '" vì vẫn còn ' . $ncc->sachs_count . ' sách liên kết.');
        }

        $ten = $ncc->ten_ncc;
        $ncc->delete();

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Đã xóa nhà cung cấp "' . $ten . '" thành công!');
    }
}
