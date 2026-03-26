<?php

namespace App\Http\Controllers;

use App\Models\TheLoai;
use Illuminate\Http\Request;

class TheLoaiController extends Controller
{
    /**
     * Hiển thị danh sách thể loại dạng cây.
     */
    public function index()
    {
        $theLoaiChas = TheLoai::with(['children.sachs', 'sachs'])
                        ->whereNull('parent_id')
                        ->orderBy('ten_the_loai')
                        ->get();

        $tongCon = TheLoai::whereNotNull('parent_id')->count();

        return view('admin.categories', compact('theLoaiChas', 'tongCon'));
    }

    /**
     * Hiển thị form thêm thể loại.
     */
    public function create()
    {
        // Lấy danh mục cha để hiển thị trong select
        $theLoaiChas = TheLoai::whereNull('parent_id')
                        ->orderBy('ten_the_loai')
                        ->get();

        return view('admin.add-the-loai', compact('theLoaiChas'));
    }

    /**
     * Lưu thể loại mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_the_loai' => 'required|string|max:150',
            'parent_id'    => 'nullable|exists:the_loai,id',
        ], [
            'ten_the_loai.required' => 'Tên thể loại là bắt buộc.',
            'ten_the_loai.max'      => 'Tên thể loại không quá 150 ký tự.',
            'parent_id.exists'      => 'Danh mục cha không hợp lệ.',
        ]);

        TheLoai::create($validated);

        return redirect()
            ->route('admin.the-loai.index')
            ->with('success', 'Thêm thể loại "' . $validated['ten_the_loai'] . '" thành công!');
    }

    /**
     * Cập nhật thể loại.
     */
    public function update(Request $request, $id)
    {
        $theLoai = TheLoai::findOrFail($id);

        $validated = $request->validate([
            'ten_the_loai' => 'required|string|max:150',
            'parent_id'    => 'nullable|exists:the_loai,id',
        ], [
            'ten_the_loai.required' => 'Tên thể loại là bắt buộc.',
            'ten_the_loai.max'      => 'Tên thể loại không quá 150 ký tự.',
            'parent_id.exists'      => 'Danh mục cha không hợp lệ.',
        ]);

        // Không cho phép lấy chính nó hoặc con của nó làm cha
        if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
            return back()->with('error', 'Không thể chọn chính thể loại này làm danh mục cha.');
        }

        $theLoai->update($validated);

        return redirect()
            ->route('admin.the-loai.index')
            ->with('success', 'Cập nhật thể loại "' . $validated['ten_the_loai'] . '" thành công!');
    }

    /**
     * Xóa thể loại.
     */
    public function destroy($id)
    {
        $theLoai = TheLoai::withCount(['sachs', 'children'])->findOrFail($id);

        if ($theLoai->children_count > 0) {
            return back()->with('error', 'Không thể xóa "' . $theLoai->ten_the_loai . '" vì vẫn còn ' . $theLoai->children_count . ' thể loại phụ.');
        }

        if ($theLoai->sachs_count > 0) {
            return back()->with('error', 'Không thể xóa "' . $theLoai->ten_the_loai . '" vì vẫn còn ' . $theLoai->sachs_count . ' sách liên kết.');
        }

        $ten = $theLoai->ten_the_loai;
        $theLoai->delete();

        return redirect()
            ->route('admin.the-loai.index')
            ->with('success', 'Đã xóa thể loại "' . $ten . '" thành công!');
    }
}
