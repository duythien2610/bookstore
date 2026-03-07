<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaGiamGiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ma_giam_gia', function (Blueprint $table) {
            $table->id();
        $table->string('ma_code', 50)->unique();
        $table->enum('loai', ['percent', 'fixed']);
        $table->decimal('gia_tri', 12, 2);
        $table->date('ngay_het_han')->nullable();
        $table->integer('so_luong')->nullable();    // null = không giới hạn
        $table->integer('da_dung')->default(0);
        $table->tinyInteger('trang_thai')->default(1);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ma_giam_gia');
    }
}
