<?php

namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\TacGia;
use App\Models\NhaXuatBan;
use App\Models\NhaCungCap;
use App\Models\TheLoai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SachController extends Controller
{
    /**
     * Hiển thị danh sách sách (có bộ lọc).
     */
    public function index(Request $request)
    {
        $query = Sach::with(['tacGia', 'theLoai']);

        // Tìm kiếm theo tên sách HOẶC tên tác giả
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('tieu_de', 'like', "%{$s}%")
                  ->orWhereHas('tacGia', function ($q2) use ($s) {
                      $q2->where('ten_tac_gia', 'like', "%{$s}%");
                  });
            });
        }

        // Lọc theo thể loại (hỗ trợ nhiều thể loại và tự động lấy con)
        if ($request->filled('the_loai_id')) {
            $ids = (array) $request->the_loai_id;
            
            // Lấy thêm tất cả ID của thể loại con của các thể loại đã chọn
            $allIds = $ids;
            $childIds = TheLoai::whereIn('parent_id', $ids)->pluck('id')->toArray();
            $allIds = array_unique(array_merge($allIds, $childIds));

            $query->whereIn('the_loai_id', $allIds);
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
        $theLoais = TheLoai::whereNull('parent_id')->with('children')->orderBy('ten_the_loai')->get();

        // Đếm tổng (không filter) cho tabs
        $tongTatCa  = Sach::count();
        $tongConHang = Sach::where('so_luong_ton', '>', 0)->count();
        $tongHetHang = Sach::where('so_luong_ton', 0)->count();

        // AJAX: trả về fragment rows + count để JS swap vào tbody.
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $sachs->count(),
                'html'    => view('admin._partials.inventory_rows', compact('sachs'))->render(),
            ]);
        }

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
     * Nhập sách hàng loạt từ file JSON.
     */
    public function importJson(Request $request)
    {
        $request->validate([
            'json_files' => 'required|array',
            'json_files.*' => 'required|file|mimes:json,txt'
        ]);

        $successCount = 0;
        $duplicateCount = 0;
        $errors = [];

        foreach ($request->file('json_files') as $file) {
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (is_null($data)) {
                $errors[] = "File {$file->getClientOriginalName()} không đúng định dạng JSON.";
                continue;
            }

            $items = isset($data['tieu_de']) ? [$data] : $data;

            foreach ($items as $item) {
                if (empty($item['tieu_de'])) {
                    $errors[] = "Có một bản ghi thiếu tên sách nên bị bỏ qua.";
                    continue;
                }

                try {
                    // Kiểm tra trùng lặp (Tên + ISBN nếu có)
                    $checkQuery = Sach::where('tieu_de', $item['tieu_de']);
                    if (!empty($item['isbn'])) {
                        $checkQuery->where('isbn', $item['isbn']);
                    }
                    
                    if ($checkQuery->exists()) {
                        $duplicateCount++;
                        continue;
                    }

                    // Xử lý năm xuất bản (MySQL YEAR: 1901 - 2155)
                    $namXuatBan = $item['nam_xuat_ban'] ?? null;
                    if ($namXuatBan) {
                        // Nếu là chuỗi dài (VD: 2012007), cố gắng lấy 4 số đầu
                        if (strlen((string)$namXuatBan) > 4) {
                            $namXuatBan = substr((string)$namXuatBan, 0, 4);
                        }
                        // Kiểm tra tính hợp lệ của năm
                        if (!is_numeric($namXuatBan) || $namXuatBan < 1901 || $namXuatBan > 2155) {
                            $namXuatBan = null;
                        }
                    }

                    // Chuẩn bị dữ liệu
                    $bookData = [
                        'tieu_de'         => substr($item['tieu_de'], 0, 199),
                        'isbn'            => isset($item['isbn']) ? substr($item['isbn'], 0, 99) : null,
                        'loai_sach'       => $item['loai_sach'] ?? 'trong_nuoc',
                        'mo_ta'           => $item['mo_ta'] ?? null,
                        'gia_ban'         => $item['gia_ban'] ?? 0,
                        'gia_goc'         => $item['gia_goc'] ?? null,
                        'so_luong_ton'    => $item['so_luong_ton'] ?? 0,
                        'nam_xuat_ban'    => $namXuatBan,
                        'so_trang'        => $item['so_trang'] ?? null,
                        'hinh_thuc_bia'   => isset($item['hinh_thuc_bia']) ? substr($item['hinh_thuc_bia'], 0, 49) : null,
                        'link_anh_bia'    => $item['link_anh_bia'] ?? null,
                        'tac_gia_id'      => null,
                        'the_loai_id'     => null,
                        'nha_xuat_ban_id' => null,
                        'nha_cung_cap_id' => null,
                    ];

                    // Xử lý quan hệ
                    if (!empty($item['ten_tac_gia'])) {
                        $tg = TacGia::firstOrCreate(['ten_tac_gia' => $item['ten_tac_gia']]);
                        $bookData['tac_gia_id'] = $tg->id;
                    } elseif (!empty($item['tac_gia_id'])) {
                        $bookData['tac_gia_id'] = is_numeric($item['tac_gia_id']) 
                            ? $item['tac_gia_id'] 
                            : TacGia::firstOrCreate(['ten_tac_gia' => $item['tac_gia_id']])->id;
                    }

                    if (!empty($item['ten_the_loai'])) {
                        $tl = TheLoai::firstOrCreate(['ten_the_loai' => $item['ten_the_loai']]);
                        $bookData['the_loai_id'] = $tl->id;
                    } elseif (!empty($item['the_loai_id'])) {
                        $bookData['the_loai_id'] = is_numeric($item['the_loai_id']) 
                            ? $item['the_loai_id'] 
                            : TheLoai::firstOrCreate(['ten_the_loai' => $item['the_loai_id']])->id;
                    }

                    if (!empty($item['ten_nxb'])) {
                        $nxb = NhaXuatBan::firstOrCreate(['ten_nxb' => $item['ten_nxb']]);
                        $bookData['nha_xuat_ban_id'] = $nxb->id;
                    } elseif (!empty($item['nha_xuat_ban_id'])) {
                        $bookData['nha_xuat_ban_id'] = is_numeric($item['nha_xuat_ban_id']) 
                            ? $item['nha_xuat_ban_id'] 
                            : NhaXuatBan::firstOrCreate(['ten_nxb' => $item['nha_xuat_ban_id']])->id;
                    }

                    if (!empty($item['ten_ncc'])) {
                        $ncc = NhaCungCap::firstOrCreate(['ten_ncc' => $item['ten_ncc']]);
                        $bookData['nha_cung_cap_id'] = $ncc->id;
                    } elseif (!empty($item['nha_cung_cap_id'])) {
                        $bookData['nha_cung_cap_id'] = is_numeric($item['nha_cung_cap_id']) 
                            ? $item['nha_cung_cap_id'] 
                            : NhaCungCap::firstOrCreate(['ten_ncc' => $item['nha_cung_cap_id']])->id;
                    }

                    Sach::create($bookData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Lỗi nhập sách '{$item['tieu_de']}': " . $e->getMessage();
                }
            }
        }

        $msg = "Nhập sách hoàn tất! Thành công: $successCount.";
        if ($duplicateCount > 0) $msg .= " Trùng lặp: $duplicateCount.";
        
        if (count($errors) > 0) {
            return redirect()->back()->with('success', $msg)->withErrors($errors);
        }

        return redirect()->back()->with('success', $msg);
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
     * Tìm kiếm sách bằng AJAX (Live search).
     */
    public function searchAjax(Request $request)
    {
        $queryText = $request->input('q');

        if (empty($queryText)) {
            return response()->json([]);
        }

        // Tìm kiếm sách theo tên hoặc tên tác giả
        $sachs = Sach::with('tacGia')
            ->where(function($q) use ($queryText) {
                $q->where('tieu_de', 'like', '%' . $queryText . '%')
                  ->orWhereHas('tacGia', function($sq) use ($queryText) {
                      $sq->where('ten_tac_gia', 'like', '%' . $queryText . '%');
                  });
            })
            ->orderByRaw("CASE 
                WHEN tieu_de LIKE ? THEN 1 
                ELSE 2 
            END", [$queryText . '%'])
            ->orderBy('tieu_de', 'asc')
            ->limit(10)
            ->get();

        // Chuẩn hóa dữ liệu trả về để JS dễ xử lý
        $results = $sachs->map(function($book) {
            return [
                'id' => $book->id,
                'tieu_de' => $book->tieu_de,
                'ten_tac_gia' => $book->tacGia ? $book->tacGia->ten_tac_gia : 'Chưa cập nhật',
                'file_anh_bia' => $book->file_anh_bia,
                'link_anh_bia' => $book->link_anh_bia,
                'gia_ban' => $book->gia_ban,
            ];
        });

        return response()->json($results);
    }

    /**
     * Hiển thị danh sách sách phía người dùng (có tìm kiếm và lọc).
     */
    public function list(Request $request)
    {
        $queryText = $request->input('search');
        
        $query = Sach::with(['tacGia', 'theLoai']);

        // Tìm kiếm theo tên sách hoặc tên tác giả
        if (!empty($queryText)) {
            $query->where(function($q) use ($queryText) {
                $q->where('tieu_de', 'like', '%' . $queryText . '%')
                  ->orWhereHas('tacGia', function($sq) use ($queryText) {
                      $sq->where('ten_tac_gia', 'like', '%' . $queryText . '%');
                  });
            });
            // Ưu tiên khớp từ đầu trong sắp xếp
            $query->orderByRaw("CASE 
                WHEN tieu_de LIKE ? THEN 1 
                ELSE 2 
            END", [$queryText . '%']);
        }

        // Lọc theo thể loại (nếu có)
        if ($request->filled('category')) {
            $categoryNames = (array)$request->category;
            $categoryIds = TheLoai::whereIn('ten_the_loai', $categoryNames)->pluck('id')->toArray();
            
            // Lấy thêm tất cả ID của thể loại con của các thể loại đã chọn
            $allIds = $categoryIds;
            $childIds = TheLoai::whereIn('parent_id', $categoryIds)->pluck('id')->toArray();
            $allIds = array_merge($allIds, $childIds);

            $query->whereIn('the_loai_id', $allIds);
        }

        // Lọc theo khoảng giá
        if ($request->filled('gia_min')) {
            $query->where('gia_ban', '>=', $request->gia_min);
        }
        if ($request->filled('gia_max')) {
            $query->where('gia_ban', '<=', $request->gia_max);
        }

        // Lọc theo đánh giá
        if ($request->filled('rating')) {
            $query->whereRaw('(SELECT AVG(so_sao) FROM danh_gia WHERE danh_gia.sach_id = sach.id) >= ?', [$request->rating]);
        }

        // Sắp xếp
        switch ($request->input('sap_xep', 'moi_nhat')) {
            case 'gia_tang':
                $query->orderBy('gia_ban', 'asc');
                break;
            case 'gia_giam':
                $query->orderBy('gia_ban', 'desc');
                break;
            default: // moi_nhat
                $query->orderByDesc('created_at');
                break;
        }

        $sachs = $query->paginate(12)->withQueryString();
        
        $theLoais = TheLoai::whereNull('parent_id')->with('children')->orderBy('ten_the_loai')->get();
        $activeCoupon = $this->getActiveCoupon();

        if ($request->ajax()) {
            return view('partials._product_grid', compact('sachs', 'activeCoupon', 'queryText'))->render();
        }

        return view('pages.product-listing', compact('sachs', 'theLoais', 'queryText', 'activeCoupon'));
    }

    /**
     * Trang "Xem tất cả sách bán chạy" — xếp theo đơn da_giao/hoan_thanh
     */
    public function bestSelling()
    {
        $sachs = Sach::mostSold(40)->with(['tacGia', 'theLoai'])->get();
        $activeCoupon = $this->getActiveCoupon();
        return view('pages.best-selling', compact('sachs', 'activeCoupon'));
    }

    public function featured()
    {
        // Lấy top 15 sách nổi bật (sử dụng scope trong Model)
        $sachs = Sach::mostSold(15)->with(['tacGia', 'theLoai'])->get();
        $activeCoupon = $this->getActiveCoupon();

        return view('pages.featured-books', compact('sachs', 'activeCoupon'));
    }

    /**
     * Hiển thị chi tiết sách (trang người dùng).
     */
    public function show($id)
    {
        $sach = Sach::with(['tacGia', 'theLoai', 'nhaXuatBan'])->findOrFail($id);

        // Lấy tất cả đánh giá kèm user
        $danhGias = $sach->danhGias()->with('user')->latest()->get();

        // Điểm trung bình
        $diemTrungBinh = $danhGias->avg('so_sao') ?? 0;

        // Phân phối sao (1-5)
        $phanPhoiSao = $danhGias->groupBy('so_sao')->map->count()->toArray();

        // Sách liên quan (cùng thể loại, loại trừ sách hiện tại)
        $sachLienQuan = Sach::with(['tacGia'])
            ->where('id', '!=', $sach->id)
            ->when($sach->the_loai_id, fn($q) => $q->where('the_loai_id', $sach->the_loai_id))
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        // Kiểm tra user đã gửi đánh giá chưa
        $daGuiDanhGia = false;
        $daMua = false;

        if (Auth::check()) {
            $userId = Auth::id();
            $daGuiDanhGia = $danhGias->where('user_id', $userId)->isNotEmpty();

            // Kiểm tra đã mua sách này chưa (đơn hàng đã giao)
            $daMua = \App\Models\DonHang::where('user_id', $userId)
                ->where('trang_thai', 'da_giao')
                ->whereHas('chiTiets', fn($q) => $q->where('sach_id', $sach->id))
                ->exists();
        }

        $activeCoupon = $this->getActiveCoupon();

        return view('pages.product-detail', compact(
            'sach',
            'danhGias',
            'diemTrungBinh',
            'phanPhoiSao',
            'sachLienQuan',
            'daGuiDanhGia',
            'daMua',
            'activeCoupon'
        ));
    }

    // =========================================================================
    //  Helper: lấy mã khuyến mãi đang hoạt động cao nhất (dùng chung)
    // =========================================================================
    private function getActiveCoupon(): ?\App\Models\MaGiamGia
    {
        return \App\Models\MaGiamGia::where('trang_thai', 1)
            ->where(function ($q) {
                $q->whereNull('ngay_het_han')
                  ->orWhere('ngay_het_han', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('so_luong')
                  ->orWhereRaw('da_dung < so_luong');
            })
            ->orderBy('gia_tri', 'desc')
            ->first();
    }
}
