<?php

namespace App\Http\Controllers;

use App\Models\MaGiamGia;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = MaGiamGia::orderByDesc('created_at');

        // Lọc theo mã code (case-insensitive, partial match).
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('ma_code', 'like', "%{$s}%");
        }

        $coupons = $query->get();

        // AJAX: trả về fragment rows + count để JS swap vào tbody.
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $coupons->count(),
                'html'    => view('admin._partials.coupons_rows', compact('coupons'))->render(),
            ]);
        }

        return view('admin.coupons', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_code'             => 'required|string|max:50|unique:ma_giam_gia,ma_code',
            'loai'                => 'required|in:percent,fixed',
            'gia_tri'             => 'required|numeric|min:0',
            'ngay_het_han'        => 'nullable|date',
            'so_luong'            => 'nullable|integer|min:1',
            'pham_vi'             => 'required|in:all,category,book',
            'the_loai_ids'        => 'nullable|array',
            'the_loai_ids.*'      => 'integer|exists:the_loai,id',
            'sach_ids'            => 'nullable|array',
            'sach_ids.*'          => 'integer|exists:sach,id',
            'dieu_kien_tai_khoan' => 'nullable|in:new,verified',
            'don_hang_toi_thieu'  => 'nullable|numeric|min:0',
        ], [
            'ma_code.required'    => 'Mã coupon là bắt buộc.',
            'ma_code.unique'      => 'Mã coupon này đã tồn tại.',
            'loai.required'       => 'Loại giảm giá là bắt buộc.',
            'gia_tri.required'    => 'Giá trị giảm là bắt buộc.',
            'ngay_het_han.after_or_equal' => 'Hạn dùng phải từ hôm nay trở đi.',
        ]);

        MaGiamGia::create([
            'ma_code'             => strtoupper(trim($request->ma_code)),
            'loai'                => $request->loai,
            'gia_tri'             => $request->gia_tri,
            'ngay_het_han'        => $request->filled('ngay_het_han') ? $request->ngay_het_han : null,
            'so_luong'            => $request->filled('so_luong') ? (int)$request->so_luong : null,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => $request->pham_vi ?? 'all',
            'the_loai_ids'        => ($request->pham_vi === 'category') ? $request->the_loai_ids : null,
            'sach_ids'            => ($request->pham_vi === 'book') ? $request->sach_ids : null,
            'dieu_kien_tai_khoan' => $request->filled('dieu_kien_tai_khoan') ? $request->dieu_kien_tai_khoan : null,
            'don_hang_toi_thieu'  => $request->filled('don_hang_toi_thieu') ? (float)$request->don_hang_toi_thieu : null,
        ]);

        return back()->with('success', 'Tạo mã giảm giá "'  . strtoupper($request->ma_code) . '" thành công!');
    }

    public function toggleStatus($id)
    {
        $ma = MaGiamGia::findOrFail($id);
        $ma->update(['trang_thai' => $ma->trang_thai ? 0 : 1]);
        return back()->with('success', 'Đã cập nhật trạng thái mã giảm giá.');
    }

    public function destroy($id)
    {
        MaGiamGia::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa mã giảm giá.');
    }

    // =========================================================================
    //  Export CSV
    // =========================================================================
    public function exportCsv()
    {
        $coupons = MaGiamGia::orderByDesc('created_at')->get();

        $filename = 'ma_giam_gia_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($coupons) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            // Header row
            fputcsv($file, ['ma_code', 'loai', 'gia_tri', 'ngay_het_han', 'so_luong', 'da_dung', 'trang_thai']);

            foreach ($coupons as $c) {
                fputcsv($file, [
                    $c->ma_code,
                    $c->loai,
                    $c->gia_tri,
                    $c->ngay_het_han ? \Carbon\Carbon::parse($c->ngay_het_han)->format('Y-m-d') : '',
                    $c->so_luong ?? '',
                    $c->da_dung,
                    $c->trang_thai ? '1' : '0',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    //  Import CSV
    // =========================================================================
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'csv_file.required' => 'Vui lòng chọn file CSV.',
            'csv_file.mimes'    => 'File phải có định dạng .csv',
            'csv_file.max'      => 'File không được vượt quá 2MB.',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');

        // Skip BOM if present
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $header = fgetcsv($file); // read header row
        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($file)) !== false) {
            $row++;
            if (count($data) < 3) {
                $errors[] = "Dòng $row: Thiếu dữ liệu.";
                $skipped++;
                continue;
            }

            [$ma_code, $loai, $gia_tri] = $data;
            $ngay_het_han = $data[3] ?? null;
            $so_luong     = $data[4] ?? null;
            $da_dung      = $data[5] ?? 0;
            $trang_thai   = isset($data[6]) ? (int)$data[6] : 1;

            $ma_code = strtoupper(trim($ma_code));

            if (empty($ma_code) || !in_array($loai, ['percent', 'fixed']) || !is_numeric($gia_tri)) {
                $errors[] = "Dòng $row: Dữ liệu không hợp lệ (ma_code=$ma_code, loai=$loai, gia_tri=$gia_tri).";
                $skipped++;
                continue;
            }

            if (MaGiamGia::where('ma_code', $ma_code)->exists()) {
                $errors[] = "Dòng $row: Mã '$ma_code' đã tồn tại, bỏ qua.";
                $skipped++;
                continue;
            }

            MaGiamGia::create([
                'ma_code'      => $ma_code,
                'loai'         => $loai,
                'gia_tri'      => (float)$gia_tri,
                'ngay_het_han' => $ngay_het_han ?: null,
                'so_luong'     => $so_luong ?: null,
                'da_dung'      => (int)$da_dung,
                'trang_thai'   => $trang_thai,
            ]);
            $imported++;
        }

        fclose($file);

        $message = "Nhập thành công $imported mã.";
        if ($skipped > 0) {
            $message .= " Bỏ qua $skipped dòng.";
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    // =========================================================================
    //  API: Lấy danh sách mã khả dụng cho khách hàng (AJAX)
    //  Trả về 2 nhóm:
    //    - applicable:     mã đang có thể áp dụng cho giỏ hiện tại
    //    - non_applicable: mã không áp được (kèm lý do)
    // =========================================================================
    public function availableForCart()
    {
        $now    = Carbon::now();
        $user   = Auth::user();
        $userId = $user?->id;

        // Tổng tiền giỏ hàng hiện tại (để tính đơn tối thiểu)
        $total = 0;
        if ($userId) {
            $gh = \App\Models\GioHang::where('user_id', $userId)
                ->where('trang_thai', 'active')
                ->first();
            $total = $gh ? (float)$gh->chiTiets()->sum('thanh_tien') : 0;
        }

        // Lấy tất cả mã còn hạn, còn lượt, đang kích hoạt
        $coupons = MaGiamGia::where('trang_thai', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('ngay_het_han')
                  ->orWhere('ngay_het_han', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('so_luong')
                  ->orWhereRaw('da_dung < so_luong');
            })
            ->orderByDesc('gia_tri')
            ->get();

        // Các mã user đã từng dùng (chưa huỷ)
        $usedIds = $userId
            ? DonHang::where('user_id', $userId)
                ->where('trang_thai', '!=', 'huy')
                ->whereNotNull('ma_giam_gia_id')
                ->pluck('ma_giam_gia_id')
                ->toArray()
            : [];

        $mapped = $coupons->map(function ($c) use ($usedIds, $user, $total) {
            $applicable = true;
            $reason     = null;

            // 1) User đã dùng rồi
            if (in_array($c->id, $usedIds)) {
                $applicable = false;
                $reason     = 'Bạn đã dùng mã này';
            }
            // 2) Yêu cầu tài khoản mới (<30 ngày)
            elseif ($c->dieu_kien_tai_khoan === 'new') {
                if (!$user) {
                    $applicable = false;
                    $reason     = 'Chỉ dành cho tài khoản mới';
                } elseif ($user->created_at && $user->created_at->diffInDays(now()) > 30) {
                    $applicable = false;
                    $reason     = 'Chỉ dành cho tài khoản đăng ký < 30 ngày';
                }
            }
            // 3) Yêu cầu tài khoản đã xác thực email
            elseif ($c->dieu_kien_tai_khoan === 'verified') {
                if (!$user || !$user->email_verified_at) {
                    $applicable = false;
                    $reason     = 'Yêu cầu email đã xác thực';
                }
            }

            // 4) Đơn hàng tối thiểu
            if ($applicable && $c->don_hang_toi_thieu && $total < (float)$c->don_hang_toi_thieu) {
                $applicable = false;
                $missing    = (float)$c->don_hang_toi_thieu - $total;
                $reason     = 'Cần mua thêm ' . number_format($missing, 0, ',', '.') . 'đ';
            }

            // Tính giá trị giảm ước lượng (để sort)
            $discount = $c->loai === 'percent'
                ? (int)($total * $c->gia_tri / 100)
                : (int)$c->gia_tri;
            if ($total > 0) $discount = min($discount, (int)$total);

            // Điều kiện để show dưới dạng list
            $conditions = [];
            if ($c->don_hang_toi_thieu) {
                $conditions[] = 'Đơn tối thiểu ' . number_format((float)$c->don_hang_toi_thieu, 0, ',', '.') . 'đ';
            } else {
                $conditions[] = 'Không yêu cầu đơn tối thiểu';
            }
            if ($c->dieu_kien_tai_khoan === 'new')      $conditions[] = 'Dành cho tài khoản mới';
            if ($c->dieu_kien_tai_khoan === 'verified') $conditions[] = 'Tài khoản đã xác thực email';
            if ($c->pham_vi === 'category')             $conditions[] = 'Áp dụng theo thể loại';
            if ($c->pham_vi === 'book')                 $conditions[] = 'Áp dụng cho sản phẩm cụ thể';

            // % lượt đã dùng
            $usagePct = ($c->so_luong && $c->so_luong > 0)
                ? min(100, (int) round(($c->da_dung / $c->so_luong) * 100))
                : 0;

            // Hiển thị giá trị trên thẻ voucher đỏ bên trái
            if ($c->loai === 'percent') {
                $giaTriStr = rtrim(rtrim(number_format((float)$c->gia_tri, 2, '.', ''), '0'), '.');
                $bigLabel  = $giaTriStr . '%';
                $fullLabel = 'Giảm ' . $giaTriStr . '%';
            } else {
                $v = (float)$c->gia_tri;
                $bigLabel  = $v >= 1000 ? number_format($v / 1000, 0, ',', '.') . 'K' : number_format($v, 0, ',', '.');
                $fullLabel = 'Giảm ' . number_format($v, 0, ',', '.') . 'đ';
            }

            return [
                'id'              => $c->id,
                'ma_code'         => $c->ma_code,
                'loai'            => $c->loai,
                'gia_tri'         => $c->gia_tri,
                'discount_amount' => $discount,
                'big_label'       => $bigLabel,
                'full_label'      => $fullLabel,
                'het_han'         => $c->ngay_het_han ? $c->ngay_het_han->format('d/m/Y') : null,
                'het_han_text'    => $c->ngay_het_han ? 'HSD: ' . $c->ngay_het_han->format('d/m/Y') : 'Không giới hạn',
                'conditions'      => $conditions,
                'usage_pct'       => $usagePct,
                'applicable'      => $applicable,
                'reason'          => $reason,
            ];
        });

        $applicable    = $mapped->where('applicable', true)->sortByDesc('discount_amount')->values();
        $nonApplicable = $mapped->where('applicable', false)->values();

        // Gắn cờ "đề xuất tốt nhất" cho mã áp được có discount cao nhất
        if ($applicable->count() > 0) {
            $first = $applicable->first();
            $first['is_best'] = true;
            $applicable[0]    = $first;
        }

        return response()->json([
            'applicable'     => $applicable,
            'non_applicable' => $nonApplicable,
            'cart_total'     => $total,
        ]);
    }
}
