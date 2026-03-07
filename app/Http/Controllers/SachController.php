<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\NhaXuatBan;
use App\Models\NhaCungCap;
use App\Models\TheLoai;
use Illuminate\Http\Request;

class SachController extends Controller
{
    /**
     * Hiển thị danh sách sách (có bộ lọc).
     */
    public function index(Request $request)
    {
        $query = Sach::with(['tacGia', 'theLoai']);

        // Tìm kiếm theo tên sách
        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }

        // Lọc theo thể loại
        if ($request->filled('the_loai_id')) {
            $query->where('the_loai_id', $request->the_loai_id);
        }

        // Lọc theo trạng thái tồn kho
        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'con_hang') {
                $query->where('so_luong_ton', '>', 0);
            } elseif ($request->trang_thai === 'het_hang') {
                $query->where('so_luong_ton', 0);
            }
        }

        // Lọc theo khoảng giá
        if ($request->filled('gia_min')) {
            $query->where('gia_ban', '>=', $request->gia_min);
        }
        if ($request->filled('gia_max')) {
            $query->where('gia_ban', '<=', $request->gia_max);
        }

        // Sắp xếp
        switch ($request->input('sap_xep', 'moi_nhat')) {
            case 'gia_tang':
                $query->orderBy('gia_ban', 'asc');
                break;
            case 'gia_giam':
                $query->orderBy('gia_ban', 'desc');
                break;
            case 'ten_az':
                $query->orderBy('tieu_de', 'asc');
                break;
            default: // moi_nhat
                $query->orderByDesc('created_at');
                break;
        }

        $sachs = $query->get();
        $theLoais = TheLoai::orderBy('ten_the_loai')->get();

        // Đếm tổng (không filter) cho tabs
        $tongTatCa  = Sach::count();
        $tongConHang = Sach::where('so_luong_ton', '>', 0)->count();
        $tongHetHang = Sach::where('so_luong_ton', 0)->count();

        return view('admin.inventory', compact(
            'sachs', 'theLoais',
            'tongTatCa', 'tongConHang', 'tongHetHang'
        ));
    }

    /**
     * Hiển thị form thêm sách mới.
     */
    public function create()
    {
        $tacGias     = TacGia::orderBy('ten_tac_gia')->get();
        $nhaXuatBans = NhaXuatBan::orderBy('ten_nxb')->get();
        $nhaCungCaps = NhaCungCap::orderBy('ten_ncc')->get();
        $theLoais    = TheLoai::with('children')
                        ->whereNull('parent_id')
                        ->orderBy('ten_the_loai')
                        ->get();

        return view('admin.add-book', compact(
            'tacGias', 'nhaXuatBans', 'nhaCungCaps', 'theLoais'
        ));
    }

    /**
     * Lưu sách mới vào database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tieu_de'        => 'required|string|max:200',
            'isbn'           => 'nullable|string|max:20|unique:sach,isbn',
            'loai_sach'      => 'required|in:trong_nuoc,nuoc_ngoai',
            'mo_ta'          => 'nullable|string',
            'tac_gia_id'     => 'nullable|exists:tac_gia,id',
            'the_loai_id'    => 'nullable|exists:the_loai,id',
            'nha_xuat_ban_id'=> 'nullable|exists:nha_xuat_ban,id',
            'nha_cung_cap_id'=> 'nullable|exists:nha_cung_cap,id',
            'nam_xuat_ban'   => 'nullable|integer|min:1900|max:' . date('Y'),
            'so_trang'       => 'nullable|integer|min:1',
            'hinh_thuc_bia'  => 'nullable|string|max:50',
            'gia_ban'        => 'required|numeric|min:0',
            'gia_goc'        => 'nullable|numeric|min:0',
            'so_luong_ton'   => 'required|integer|min:0',
            'file_anh_bia'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link_anh_bia'   => 'nullable|url|max:200',
        ], [
            'tieu_de.required'    => 'Tên sách là bắt buộc.',
            'tieu_de.max'         => 'Tên sách không quá 200 ký tự.',
            'isbn.unique'         => 'Mã ISBN này đã tồn tại.',
            'isbn.max'            => 'Mã ISBN không quá 20 ký tự.',
            'gia_ban.required'    => 'Giá bán là bắt buộc.',
            'gia_ban.min'         => 'Giá bán không được âm.',
            'so_luong_ton.required'=> 'Số lượng tồn là bắt buộc.',
            'so_luong_ton.min'    => 'Số lượng tồn không được âm.',
            'file_anh_bia.image'  => 'File phải là ảnh.',
            'file_anh_bia.max'    => 'Ảnh bìa không quá 2MB.',
            'link_anh_bia.url'    => 'Link ảnh bìa phải là URL hợp lệ.',
        ]);

        // Xử lý upload ảnh bìa
        if ($request->hasFile('file_anh_bia')) {
            $file = $request->file('file_anh_bia');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/books'), $fileName);
            $validated['file_anh_bia'] = $fileName;
        }

        Sach::create($validated);

        return redirect()
            ->route('admin.inventory')
            ->with('success', 'Thêm sách "' . $validated['tieu_de'] . '" thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa sách.
     */
    public function edit($id)
    {
        $sach = Sach::findOrFail($id);

        $tacGias     = TacGia::orderBy('ten_tac_gia')->get();
        $nhaXuatBans = NhaXuatBan::orderBy('ten_nxb')->get();
        $nhaCungCaps = NhaCungCap::orderBy('ten_ncc')->get();
        $theLoais    = TheLoai::with('children')
                        ->whereNull('parent_id')
                        ->orderBy('ten_the_loai')
                        ->get();

        return view('admin.edit-book', compact(
            'sach', 'tacGias', 'nhaXuatBans', 'nhaCungCaps', 'theLoais'
        ));
    }

    /**
     * Cập nhật thông tin sách trong database.
     */
    public function update(Request $request, $id)
    {
        $sach = Sach::findOrFail($id);

        $validated = $request->validate([
            'tieu_de'        => 'required|string|max:200',
            'isbn'           => 'nullable|string|max:20|unique:sach,isbn,' . $id,
            'loai_sach'      => 'required|in:trong_nuoc,nuoc_ngoai',
            'mo_ta'          => 'nullable|string',
            'tac_gia_id'     => 'nullable|exists:tac_gia,id',
            'the_loai_id'    => 'nullable|exists:the_loai,id',
            'nha_xuat_ban_id'=> 'nullable|exists:nha_xuat_ban,id',
            'nha_cung_cap_id'=> 'nullable|exists:nha_cung_cap,id',
            'nam_xuat_ban'   => 'nullable|integer|min:1900|max:' . date('Y'),
            'so_trang'       => 'nullable|integer|min:1',
            'hinh_thuc_bia'  => 'nullable|string|max:50',
            'gia_ban'        => 'required|numeric|min:0',
            'gia_goc'        => 'nullable|numeric|min:0',
            'so_luong_ton'   => 'required|integer|min:0',
            'file_anh_bia'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'link_anh_bia'   => 'nullable|url|max:200',
        ], [
            'tieu_de.required'    => 'Tên sách là bắt buộc.',
            'tieu_de.max'         => 'Tên sách không quá 200 ký tự.',
            'isbn.unique'         => 'Mã ISBN này đã tồn tại.',
            'isbn.max'            => 'Mã ISBN không quá 20 ký tự.',
            'gia_ban.required'    => 'Giá bán là bắt buộc.',
            'gia_ban.min'         => 'Giá bán không được âm.',
            'so_luong_ton.required'=> 'Số lượng tồn là bắt buộc.',
            'so_luong_ton.min'    => 'Số lượng tồn không được âm.',
            'file_anh_bia.image'  => 'File phải là ảnh.',
            'file_anh_bia.max'    => 'Ảnh bìa không quá 2MB.',
            'link_anh_bia.url'    => 'Link ảnh bìa phải là URL hợp lệ.',
        ]);

        // Xử lý upload ảnh bìa mới
        if ($request->hasFile('file_anh_bia')) {
            // Xóa ảnh cũ nếu có
            if ($sach->file_anh_bia && file_exists(public_path('uploads/books/' . $sach->file_anh_bia))) {
                unlink(public_path('uploads/books/' . $sach->file_anh_bia));
            }

            $file = $request->file('file_anh_bia');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/books'), $fileName);
            $validated['file_anh_bia'] = $fileName;
        }

        $sach->update($validated);

        return redirect()
            ->route('admin.inventory')
            ->with('success', 'Cập nhật sách "' . $validated['tieu_de'] . '" thành công!');
    }
}
