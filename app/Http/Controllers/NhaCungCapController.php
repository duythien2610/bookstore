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
            ->route('admin.nha-cung-cap.create')
            ->with('success', 'Thêm nhà cung cấp "' . $validated['ten_ncc'] . '" thành công!');
    }
}
