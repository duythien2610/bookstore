<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSachTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sach', function (Blueprint $table) {
        $table->id();
        $table->string('tieu_de', 200);

        $table->foreignId('tac_gia_id')->nullable()->constrained('tac_gia');
        $table->foreignId('nha_xuat_ban_id')->nullable()->constrained('nha_xuat_ban');
        $table->foreignId('nha_cung_cap_id')->nullable()->constrained('nha_cung_cap');
        $table->foreignId('the_loai_id')->nullable()->constrained('the_loai');

        $table->string('hinh_thuc_bia', 50)->nullable();
        $table->text('mo_ta')->nullable();
        $table->string('file_anh_bia', 100)->nullable();
        $table->string('link_anh_bia', 200)->nullable();

        $table->decimal('gia_ban', 12, 2); // chuẩn tiền
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
        Schema::dropIfExists('sach');
    }
}
