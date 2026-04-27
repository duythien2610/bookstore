<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHinhAnhToDanhGiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('danh_gia', function (Blueprint $table) {
            $table->json('hinh_anh')->nullable()->after('binh_luan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('danh_gia', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
}
