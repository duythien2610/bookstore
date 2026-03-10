<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDanhGiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('danh_gia', function (Blueprint $table) {
            //
            $table->string('tieu_de', 200)->nullable()->after('so_sao');
        $table->tinyInteger('trang_thai')->default(1)->after('binh_luan');
        // Thêm CHECK constraint cho so_sao
        DB::statement('ALTER TABLE danh_gia ADD CONSTRAINT check_so_sao CHECK (so_sao BETWEEN 1 AND 5)');
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
            //
        });
    }
}
