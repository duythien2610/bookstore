<?php

namespace App\Http\Controllers;

use App\Models\MaGiamGia;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = MaGiamGia::with('theLoai')->orderByDesc('created_at')->get();
        $categories = \App\Models\TheLoai::all();
        return view('admin.coupons', compact('coupons', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_code'      => 'required|string|max:50|unique:ma_giam_gia,ma_code',
            'loai'         => 'required|in:percent,fixed',
            'gia_tri'      => 'required|numeric|min:0',
            'the_loai_id'  => 'nullable|exists:the_loai,id',
            'ngay_het_han' => 'nullable|date|after_or_equal:today',
            'so_luong'     => 'nullable|integer|min:1',
        ], [
            'ma_code.required'   => 'Mã coupon là bắt buộc.',
            'ma_code.unique'     => 'Mã coupon này đã tồn tại.',
            'loai.required'      => 'Loại giảm giá là bắt buộc.',
            'gia_tri.required'   => 'Giá trị giảm là bắt buộc.',
            'the_loai_id.exists' => 'Danh mục không hợp lệ.',
            'ngay_het_han.after_or_equal' => 'Hạn dùng phải từ hôm nay trở đi.',
        ]);

        MaGiamGia::create([
            'ma_code'      => strtoupper(trim($request->ma_code)),
            'loai'         => $request->loai,
            'gia_tri'      => $request->gia_tri,
            'the_loai_id'  => $request->the_loai_id,
            'ngay_het_han' => $request->ngay_het_han,
            'so_luong'     => $request->so_luong,
            'da_dung'      => 0,
            'trang_thai'   => 1,
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

    public function exportCSV()
    {
        $coupons = MaGiamGia::all();
        $filename = "coupons_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://temp', 'r+');
        // Thêm BOM để Excel đọc được tiếng Việt
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, ['Mã Code', 'Loại', 'Giá Trị', 'ID Thể Loại', 'Ngày Hết Hạn', 'Số Lượng', 'Đã Dùng', 'Trạng Thái']);
        foreach($coupons as $c) {
            fputcsv($handle, [
                $c->ma_code,
                $c->loai,
                $c->gia_tri,
                $c->the_loai_id,
                $c->ngay_het_han,
                $c->so_luong,
                $c->da_dung,
                $c->trang_thai
            ]);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ], [
            'csv_file.required' => 'Vui lòng chọn file CSV.',
            'csv_file.mimes'    => 'File phải định dạng CSV hoặc TXT.',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->path(), 'r');
        
        // Remove BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $header = fgetcsv($handle); // Bỏ qua header

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                // Đảm bảo không lỗi nếu các dòng thiếu cột
                $maCode = strtoupper(trim($row[0]));
                if (empty($maCode)) continue;

                MaGiamGia::updateOrCreate(
                    ['ma_code' => $maCode],
                    [
                        'loai'         => (isset($row[1]) && in_array(strtolower(trim($row[1])), ['percent', 'fixed'])) ? strtolower(trim($row[1])) : 'fixed',
                        'gia_tri'      => (isset($row[2]) && is_numeric($row[2])) ? $row[2] : 0,
                        'the_loai_id'  => (isset($row[3]) && intval($row[3]) > 0) ? intval($row[3]) : null,
                        'ngay_het_han' => (isset($row[4]) && strtotime($row[4])) ? date('Y-m-d', strtotime(str_replace('/', '-', $row[4]))) : null,
                        'so_luong'     => (isset($row[5]) && trim($row[5]) !== '') ? intval($row[5]) : null,
                        'da_dung'      => (isset($row[6]) && is_numeric($row[6])) ? intval($row[6]) : 0,
                        'trang_thai'   => (isset($row[7]) && is_numeric($row[7])) ? intval($row[7]) : 1,
                    ]
                );
                $count++;
            }
        }
        fclose($handle);

        return back()->with('success', "Đã import thành công $count mã giảm giá từ CSV.");
    }

    public function applyCheckoutVoucher(Request $request)
    {
        $code = strtoupper(trim($request->ma_code));
        $voucher = MaGiamGia::where('ma_code', $code)
            ->where('trang_thai', 1)
            ->whereNull('the_loai_id') // Ở khâu thanh toán chỉ dùng mã toàn cục (hoặc logic tùy biến)
            ->where(function($q) {
                $q->whereNull('ngay_het_han')->orWhere('ngay_het_han', '>=', date('Y-m-d'));
            })
            ->where(function($q) {
                $q->whereNull('so_luong')->orWhereRaw('da_dung < so_luong');
            })
            ->first();

        if (!$voucher) {
            return back()->with('error', 'Mã giảm giá không hợp lệ, đã hết hạn hoặc hết lượt sử dụng.');
        }

        session(['checkout_voucher' => $voucher]);
        return back()->with('success', 'Áp dụng mã giảm giá thành công! Giảm ' . 
            ($voucher->loai === 'percent' ? $voucher->gia_tri . '%' : number_format($voucher->gia_tri, 0, ',', '.') . 'đ') . ' trên tổng đơn.');
    }

    public function removeCheckoutVoucher()
    {
        session()->forget('checkout_voucher');
        return back()->with('success', 'Đã hủy áp dụng mã giảm giá.');
    }
}
