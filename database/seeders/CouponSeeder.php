<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaGiamGia;
use App\Models\Sach;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    public function run()
    {
        // ── Xóa toàn bộ dữ liệu cũ (dùng delete thay vì truncate để tránh FK constraint) ─
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MaGiamGia::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now  = Carbon::now();
        $exp1 = $now->copy()->addMonths(2)->format('Y-m-d');  // 2 tháng tới
        $exp2 = $now->copy()->addMonths(1)->format('Y-m-d');  // 1 tháng tới
        $exp3 = $now->copy()->addDays(15)->format('Y-m-d');   // 15 ngày tới

        // ── 1. Top 40 sách bán chạy / nổi bật ───────────────────────────
        $top40Ids = Sach::mostSold(40)->pluck('id')->toArray();

        // Chia thành 4 nhóm 10 cuốn, mỗi nhóm 1 mã riêng
        $chunkSize = 10;
        $chunks = array_chunk($top40Ids, $chunkSize);

        $bookCoupons = [
            ['code' => 'BESTSELLER10',  'loai' => 'percent', 'gia_tri' => 10, 'het_han' => $exp1, 'so_luong' => 200],
            ['code' => 'BESTSELLER15',  'loai' => 'percent', 'gia_tri' => 15, 'het_han' => $exp2, 'so_luong' => 150],
            ['code' => 'TOPDEAL_20K',   'loai' => 'fixed',   'gia_tri' => 20000, 'het_han' => $exp1, 'so_luong' => 300],
            ['code' => 'TOPBOOK_25PCT', 'loai' => 'percent', 'gia_tri' => 25, 'het_han' => $exp3, 'so_luong' => 100],
        ];

        foreach ($bookCoupons as $idx => $bc) {
            MaGiamGia::create([
                'ma_code'             => $bc['code'],
                'loai'                => $bc['loai'],
                'gia_tri'             => $bc['gia_tri'],
                'ngay_het_han'        => $bc['het_han'],
                'so_luong'            => $bc['so_luong'],
                'da_dung'             => 0,
                'trang_thai'          => 1,
                'pham_vi'             => 'book',
                'sach_ids'            => $chunks[$idx] ?? [],
                'the_loai_ids'        => null,
                'dieu_kien_tai_khoan' => null,
                'don_hang_toi_thieu'  => null,
            ]);
        }

        // Thêm 1 mã áp dụng cho ALL 40 cuốn cùng lúc
        MaGiamGia::create([
            'ma_code'             => 'NOIBAT40',
            'loai'                => 'percent',
            'gia_tri'             => 12,
            'ngay_het_han'        => $exp1,
            'so_luong'            => 500,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => 'book',
            'sach_ids'            => $top40Ids,
            'the_loai_ids'        => null,
            'dieu_kien_tai_khoan' => null,
            'don_hang_toi_thieu'  => null,
        ]);

        // ── 2. Mã theo 5 thể loại ────────────────────────────────────────
        // Lấy id 5 thể loại cha đầu tiên
        $catIds = \App\Models\TheLoai::whereNull('parent_id')
            ->orderBy('id')
            ->limit(5)
            ->pluck('id')
            ->toArray();

        $categoryCoupons = [
            ['code' => 'C' . $catIds[0] . '_GIAM10', 'loai' => 'percent', 'gia_tri' => 10, 'ids' => [$catIds[0]], 'het_han' => $exp1, 'so_luong' => 200],
            ['code' => 'C' . $catIds[1] . '_GIAM15', 'loai' => 'percent', 'gia_tri' => 15, 'ids' => [$catIds[1]], 'het_han' => $exp2, 'so_luong' => 150],
            ['code' => 'C' . $catIds[2] . '_15K',    'loai' => 'fixed',   'gia_tri' => 15000, 'ids' => [$catIds[2]], 'het_han' => $exp1, 'so_luong' => 100],
            ['code' => 'C' . $catIds[3] . '_GIAM20', 'loai' => 'percent', 'gia_tri' => 20, 'ids' => [$catIds[3]], 'het_han' => $exp3, 'so_luong' => 80],
            ['code' => 'C' . $catIds[4] . '_30K',    'loai' => 'fixed',   'gia_tri' => 30000, 'ids' => [$catIds[4]], 'het_han' => $exp2, 'so_luong' => 120],
        ];

        foreach ($categoryCoupons as $cc) {
            MaGiamGia::create([
                'ma_code'             => $cc['code'],
                'loai'                => $cc['loai'],
                'gia_tri'             => $cc['gia_tri'],
                'ngay_het_han'        => $cc['het_han'],
                'so_luong'            => $cc['so_luong'],
                'da_dung'             => 0,
                'trang_thai'          => 1,
                'pham_vi'             => 'category',
                'the_loai_ids'        => $cc['ids'],
                'sach_ids'            => null,
                'dieu_kien_tai_khoan' => null,
                'don_hang_toi_thieu'  => null,
            ]);
        }

        // ── 3. Mã cho tài khoản mới (đăng ký trong 30 ngày) ─────────────
        MaGiamGia::create([
            'ma_code'             => 'WELCOME20',
            'loai'                => 'percent',
            'gia_tri'             => 20,
            'ngay_het_han'        => $exp1,
            'so_luong'            => 999,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => 'all',
            'the_loai_ids'        => null,
            'sach_ids'            => null,
            'dieu_kien_tai_khoan' => 'new',
            'don_hang_toi_thieu'  => null,
        ]);

        MaGiamGia::create([
            'ma_code'             => 'NEWMEMBER50K',
            'loai'                => 'fixed',
            'gia_tri'             => 50000,
            'ngay_het_han'        => $exp1,
            'so_luong'            => 500,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => 'all',
            'the_loai_ids'        => null,
            'sach_ids'            => null,
            'dieu_kien_tai_khoan' => 'new',
            'don_hang_toi_thieu'  => 100000,
        ]);

        // ── 4. Mã áp dụng toàn bộ (all) không điều kiện ─────────────────
        MaGiamGia::create([
            'ma_code'             => 'SALE10',
            'loai'                => 'percent',
            'gia_tri'             => 10,
            'ngay_het_han'        => $exp1,
            'so_luong'            => 1000,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => 'all',
            'the_loai_ids'        => null,
            'sach_ids'            => null,
            'dieu_kien_tai_khoan' => null,
            'don_hang_toi_thieu'  => null,
        ]);

        MaGiamGia::create([
            'ma_code'             => 'FREESHIP',
            'loai'                => 'fixed',
            'gia_tri'             => 30000,
            'ngay_het_han'        => $exp2,
            'so_luong'            => 500,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => 'all',
            'the_loai_ids'        => null,
            'sach_ids'            => null,
            'dieu_kien_tai_khoan' => null,
            'don_hang_toi_thieu'  => 200000,
        ]);

        MaGiamGia::create([
            'ma_code'             => 'VIP25',
            'loai'                => 'percent',
            'gia_tri'             => 25,
            'ngay_het_han'        => $exp3,
            'so_luong'            => 200,
            'da_dung'             => 0,
            'trang_thai'          => 1,
            'pham_vi'             => 'all',
            'the_loai_ids'        => null,
            'sach_ids'            => null,
            'dieu_kien_tai_khoan' => 'verified',
            'don_hang_toi_thieu'  => 300000,
        ]);

        $this->command->info('✅ Đã tạo ' . MaGiamGia::count() . ' mã giảm giá thành công!');
    }
}
