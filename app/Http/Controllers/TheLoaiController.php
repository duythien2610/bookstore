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
            ->route('admin.the-loai.create')
            ->with('success', 'Thêm thể loại "' . $validated['ten_the_loai'] . '" thành công!');
    }
}
