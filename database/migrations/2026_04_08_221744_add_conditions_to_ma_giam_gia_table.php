<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionsToMaGiamGiaTable extends Migration
{
    public function up()
    {
        Schema::table('ma_giam_gia', function (Blueprint $table) {
            // Phạm vi áp dụng: 'all' | 'category' | 'book'
            $table->string('pham_vi', 20)->default('all')->after('trang_thai');
            // ID thể loại (JSON array) khi pham_vi = 'category'
            $table->text('the_loai_ids')->nullable()->after('pham_vi');
            // ID sách (JSON array) khi pham_vi = 'book'
            $table->text('sach_ids')->nullable()->after('the_loai_ids');

            // Điều kiện tài khoản: null = không giới hạn
            // 'new' = tài khoản mới (đăng ký trong 30 ngày)
            // 'verified' = đã xác thực email
            $table->string('dieu_kien_tai_khoan', 30)->nullable()->after('sach_ids');

            // Giá trị đơn hàng tối thiểu để áp dụng
            $table->decimal('don_hang_toi_thieu', 12, 2)->nullable()->after('dieu_kien_tai_khoan');
        });
    }

    public function down()
    {
        Schema::table('ma_giam_gia', function (Blueprint $table) {
            $table->dropColumn([
                'pham_vi', 'the_loai_ids', 'sach_ids',
                'dieu_kien_tai_khoan', 'don_hang_toi_thieu'
            ]);
        });
    }
}
