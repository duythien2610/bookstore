<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\NhaXuatBan;
use App\Models\NhaCungCap;
use App\Models\TheLoai;
use App\Models\DanhGia;
use Illuminate\Http\Request;

class SachController extends Controller
{
    // =========================================================================
    //  PUBLIC FACING — Danh sách sách (có filter, sort, paginate)
    // =========================================================================

    public function indexPublic(Request $request)
    {
        $filters = $request->only([
            'search', 'the_loai_id', 'loai_sach', 'gia_min', 'gia_max'
        ]);

        $sortType = $request->input('sort', 'moi_nhat');

        $sachs = Sach::with(['tacGia', 'theLoai', 'nhaXuatBan'])
            ->where('so_luong_ton', '>', 0)
            ->filter($filters)
            ->sortType($sortType)
            ->paginate(12)
            ->withQueryString();

        // Sidebar: thể loại phân cấp (dùng biến đã có từ AppServiceProvider)
        // Thông tin thể loại đang active (fór breadcrumb và tiêu đề)
        $activeCategory = null;
        if ($request->filled('the_loai_id')) {
            $activeCategory = TheLoai::find($request->the_loai_id);
        }

        return view('pages.product-listing', compact('sachs', 'activeCategory'));
    }

    // =========================================================================
    //  PUBLIC FACING — Chi tiết sách
    // =========================================================================

    public function showPublic($id)
    {
        $sach = Sach::with(['tacGia', 'nhaXuatBan', 'nhaCungCap', 'theLoai'])->findOrFail($id);

        // Sách liên quan: cùng thể loại, khác id hiện tại
        $sachLienQuan = Sach::with('tacGia')
            ->where('the_loai_id', $sach->the_loai_id)
            ->where('id', '!=', $sach->id)
            ->where('so_luong_ton', '>', 0)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        // Đánh giá của sách (chỉ duyệt)
        $danhGias = DanhGia::with('user')
            ->where('sach_id', $sach->id)
            ->where('trang_thai', 1)
            ->orderByDesc('created_at')
            ->get();

        $diemTrungBinh = $danhGias->avg('so_sao') ?? 0;

        // Phân phối sao
        $phanPhoiSao = [];
        for ($i = 5; $i >= 1; $i--) {
            $phanPhoiSao[$i] = $danhGias->where('so_sao', $i)->count();
        }

        // Kiểm tra user đã mua sách này chưa (để cho phép đánh giá)
        $daMua = false;
        $daGuiDanhGia = false;
        if (auth()->check()) {
            $userId = auth()->id();
            $daMua = \App\Models\DonHangChiTiet::whereHas('donHang', function($q) use ($userId) {
                $q->where('user_id', $userId)->whereIn('trang_thai', ['da_giao','dang_giao','dang_xu_ly','cho_xac_nhan']);
            })->where('sach_id', $sach->id)->exists();

            $daGuiDanhGia = DanhGia::where('user_id', $userId)->where('sach_id', $sach->id)->exists();
        }

        return view('pages.product-detail', compact('sach', 'sachLienQuan', 'danhGias', 'diemTrungBinh', 'phanPhoiSao', 'daMua', 'daGuiDanhGia'));
    }

    // =========================================================================
    //  ADMIN — Hiển thị danh sách sách (có bộ lọc)
    // =========================================================================

    public function index(Request $request)
    {
        $filters = $request->only([
            'search', 'the_loai_id', 'trang_thai', 'gia_min', 'gia_max'
        ]);

        $sortType = $request->input('sap_xep', 'moi_nhat');

        $sachs = Sach::with(['tacGia', 'theLoai'])
                    ->filter($filters)
                    ->sortType($sortType)
                    ->get();

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

    /**
     * Nhập sách từ file JSON.
     */
    public function importJson(Request $request)
    {
        $request->validate([
            'json_files' => 'required|array',
            'json_files.*' => 'file|mimes:json,txt',
        ], [
            'json_files.required' => 'Vui lòng chọn ít nhất một file JSON.',
            'json_files.*.mimes'    => 'Các file phải có định dạng .json hoặc .txt',
        ]);

        $successCount = 0;
        $duplicateCount = 0;
        $errors = [];
        
        $cachedTheLoais = TheLoai::all();

        foreach ($request->file('json_files') as $file) {
            $fileName = $file->getClientOriginalName();
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "File '$fileName' không hợp lệ: " . json_last_error_msg();
                continue;
            }

            $items = isset($data[0]) ? $data : [$data];

            foreach ($items as $index => $item) {
                try {
                    // 1. Kiểm tra format
                    if (empty($item['tieu_de'])) {
                        $errors[] = "File '$fileName' - Dòng " . ($index + 1) . ": Sai định dạng (Thiếu tên sách).";
                        continue;
                    }

                    // 2. Kiểm tra trùng lặp
                    if (Sach::where('tieu_de', $item['tieu_de'])->orWhere('isbn', $item['isbn'] ?? '---')->exists()) {
                        $duplicateCount++;
                        continue; 
                    }

                    // 3. Giải quyết Tác giả, NXB, NCC
                    foreach ([
                        ['name' => 'ten_tac_gia', 'id' => 'tac_gia_id', 'model' => TacGia::class, 'col' => 'ten_tac_gia'],
                        ['name' => 'ten_nxb', 'id' => 'nha_xuat_ban_id', 'model' => NhaXuatBan::class, 'col' => 'ten_nxb'],
                        ['name' => 'ten_ncc', 'id' => 'nha_cung_cap_id', 'model' => NhaCungCap::class, 'col' => 'ten_ncc'],
                    ] as $entity) {
                        $val = $item[$entity['name']] ?? ($item[$entity['id']] ?? null);
                        if ($val && !is_numeric($val)) {
                            $obj = $entity['model']::firstOrCreate([$entity['col'] => trim($val)]);
                            $item[$entity['id']] = $obj->id;
                        }
                    }

                    // 4. Giải quyết Thể loại (Fuzzy Match)
                    $tlVal = $item['ten_the_loai'] ?? ($item['the_loai_id'] ?? null);
                    if ($tlVal && !is_numeric($tlVal)) {
                        $inputNorm = $this->normalizeString($tlVal);
                        $match = null;
                        foreach ($cachedTheLoais as $tl) {
                            $dbNorm = $this->normalizeString($tl->ten_the_loai);
                            if ($dbNorm === $inputNorm || strpos($dbNorm, $inputNorm) !== false || strpos($inputNorm, $dbNorm) !== false) {
                                $match = $tl;
                                break;
                            }
                        }
                        if ($match) {
                            $item['the_loai_id'] = $match->id;
                        } else {
                            $newTl = TheLoai::create(['ten_the_loai' => trim($tlVal)]);
                            $item['the_loai_id'] = $newTl->id;
                            $cachedTheLoais->push($newTl);
                        }
                    }

                    // Clean-up IDs
                    foreach(['tac_gia_id', 'nha_xuat_ban_id', 'nha_cung_cap_id', 'the_loai_id'] as $f) {
                        if (isset($item[$f]) && !is_numeric($item[$f])) $item[$f] = null;
                    }

                    // Gán giá trị mặc định cho các trường số
                    if (!isset($item['gia_ban'])) $item['gia_ban'] = 0;
                    if (!isset($item['so_luong_ton'])) $item['so_luong_ton'] = 0;
                    if (!isset($item['loai_sach'])) $item['loai_sach'] = 'trong_nuoc';

                    Sach::create($item);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "File '$fileName' - Dòng " . ($index + 1) . ": Lỗi hệ thống (" . $e->getMessage() . ")";
                }
            }
        }

        $msg = "Hoàn tất: Đã thêm mới " . $successCount . " cuốn từ các file.";
        if ($duplicateCount > 0) {
            $msg .= " (Phát hiện " . $duplicateCount . " cuốn đã tồn tại trong hệ thống).";
        }

        if ($successCount > 0 || $duplicateCount > 0) {
            return redirect()->route('admin.inventory')->with('success', $msg)->withErrors($errors);
        }

        return back()->withErrors($errors ?: ['Không có dữ liệu hợp lệ để nhập.']);
    }

    /**
     * Chuẩn hóa chuỗi (Xóa dấu, chuyển thường, xóa ký tự đặc biệt) để so sánh.
     */
    private function normalizeString($str)
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|ã|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        // Xóa các ký tự đặc biệt, gạch ngang, khoảng trắng thừa
        $str = preg_replace('/[^a-z0-9]/', '', $str);
        return $str;
    }

    /**
     * Xóa sách khỏi database.
     */
    public function destroy($id)
    {
        $sach = Sach::findOrFail($id);
        $tenSach = $sach->tieu_de;

        // Xóa ảnh bìa nếu có
        if ($sach->file_anh_bia && file_exists(public_path('uploads/books/' . $sach->file_anh_bia))) {
            unlink(public_path('uploads/books/' . $sach->file_anh_bia));
        }

        $sach->delete();

        return redirect()
            ->route('admin.inventory')
            ->with('success', 'Đã xóa sách "' . $tenSach . '" thành công!');
    }
}
