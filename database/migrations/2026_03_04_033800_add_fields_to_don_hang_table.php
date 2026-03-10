<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDonHangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('don_hang', function (Blueprint $table) {
            //
            $table->string('dia_chi_giao', 255)->after('tong_tien');
            $table->string('phuong_thuc_tt', 50)->nullable()->after('dia_chi_giao');
            $table->string('trang_thai_tt', 50)->default('chua_thanh_toan')->after('phuong_thuc_tt');
            $table->text('ghi_chu')->nullable()->after('trang_thai_tt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('don_hang', function (Blueprint $table) {
            //
            $table->dropColumn('dia_chi_giao');
            $table->dropColumn('phuong_thuc_tt');
            $table->dropColumn('trang_thai_tt');
            $table->dropColumn('ghi_chu');
        });
    }
}
