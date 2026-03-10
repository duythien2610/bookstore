<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGioHangChiTietTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gio_hang_chi_tiet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gio_hang_id')->constrained('gio_hang')->cascadeOnDelete();
            $table->foreignId('sach_id')->constrained('sach')->cascadeOnDelete();
            $table->integer('so_luong');
            $table->decimal('don_gia',12,2);
            $table->decimal('thanh_tien',12,2);
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
        Schema::dropIfExists('gio_hang_chi_tiet');
    }
}
