<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonHangChiTietTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('don_hang_chi_tiet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_hang_id')->constrained('don_hang')->cascadeOnDelete();
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
        Schema::dropIfExists('don_hang_chi_tiet');
    }
}
