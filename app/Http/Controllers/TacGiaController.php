<?php

namespace App\Http\Controllers;

use App\Models\TacGia;
use Illuminate\Http\Request;

class TacGiaController extends Controller
{
    /**
     * Hiển thị danh sách đối tác (tác giả, NXB, NCC).
     */
    public function index()
    {
        $tacGias     = TacGia::with('sachs')->orderBy('ten_tac_gia')->get();
        $nhaXuatBans = \App\Models\NhaXuatBan::with('sachs')->orderBy('ten_nxb')->get();
        $nhaCungCaps = \App\Models\NhaCungCap::with('sachs')->orderBy('ten_ncc')->get();

        return view('admin.partners', compact('tacGias', 'nhaXuatBans', 'nhaCungCaps'));
    }

    /**
     * Hiển thị form thêm tác giả.
     */
    public function create()
    {
        return view('admin.add-tac-gia');
    }

    /**
     * Lưu tác giả mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_tac_gia' => 'required|string|max:150|unique:tac_gia,ten_tac_gia',
        ], [
            'ten_tac_gia.required' => 'Tên tác giả là bắt buộc.',
            'ten_tac_gia.max'      => 'Tên tác giả không quá 150 ký tự.',
            'ten_tac_gia.unique'   => 'Tác giả này đã tồn tại.',
        ]);

        TacGia::create($validated);

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Thêm tác giả "' . $validated['ten_tac_gia'] . '" thành công!');
    }

    /**
     * Cập nhật tác giả.
     */
    public function update(Request $request, $id)
    {
        $tacGia = TacGia::findOrFail($id);

        $validated = $request->validate([
            'ten_tac_gia' => 'required|string|max:150|unique:tac_gia,ten_tac_gia,' . $id,
        ], [
            'ten_tac_gia.required' => 'Tên tác giả là bắt buộc.',
            'ten_tac_gia.max'      => 'Tên tác giả không quá 150 ký tự.',
            'ten_tac_gia.unique'   => 'Tác giả này đã tồn tại.',
        ]);

        $tacGia->update($validated);

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Cập nhật tác giả "' . $validated['ten_tac_gia'] . '" thành công!');
    }

    /**
     * Xóa tác giả.
     */
    public function destroy($id)
    {
        $tacGia = TacGia::withCount('sachs')->findOrFail($id);

        if ($tacGia->sachs_count > 0) {
            return back()->with('error', 'Không thể xóa tác giả "' . $tacGia->ten_tac_gia . '" vì vẫn còn ' . $tacGia->sachs_count . ' sách liên kết.');
        }

        $ten = $tacGia->ten_tac_gia;
        $tacGia->delete();

        return redirect()
            ->route('admin.partners')
            ->with('success', 'Đã xóa tác giả "' . $ten . '" thành công!');
    }
}
