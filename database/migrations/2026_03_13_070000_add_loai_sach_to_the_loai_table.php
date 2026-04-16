<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLoaiSachToTheLoaiTable extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột loai_sach vào bảng the_loai để phân biệt thể loại sách trong nước / nước ngoài.
     * Giá trị: 'trong_nuoc', 'nuoc_ngoai', 'tat_ca'
     */
    public function up()
    {
        Schema::table('the_loai', function (Blueprint $table) {
            $table->enum('loai_sach', ['trong_nuoc', 'nuoc_ngoai', 'tat_ca'])
                  ->default('trong_nuoc')
                  ->after('ten_the_loai')
                  ->comment('Phân loại thể loại: trong_nuoc | nuoc_ngoai | tat_ca');
        });

        // Cập nhật dữ liệu: các thể loại tiếng Anh (id 39,44,47 và con của chúng) → nuoc_ngoai
        // Thể loại nước ngoài cha: FICTION=39, Business & Management=44, Personal Development=47
        $nuocNgoaiParentIds = [39, 44, 47];

        // Cập nhật thể loại cha
        DB::table('the_loai')
            ->whereIn('id', $nuocNgoaiParentIds)
            ->update(['loai_sach' => 'nuoc_ngoai']);

        // Cập nhật thể loại con của các thể loại cha trên
        DB::table('the_loai')
            ->whereIn('parent_id', $nuocNgoaiParentIds)
            ->update(['loai_sach' => 'nuoc_ngoai']);

        // Các thể loại còn lại (cha tiếng Việt và con của chúng) đã có default = 'trong_nuoc'
        // Không cần cập nhật thêm nếu DB đã có đủ default
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('the_loai', function (Blueprint $table) {
            $table->dropColumn('loai_sach');
        });
    }
}
