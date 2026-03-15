<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de', 200)->nullable();         // Tiêu đề banner
            $table->string('mo_ta', 500)->nullable();          // Mô tả / sub-title
            $table->string('duong_dan_anh', 500)->nullable();  // Đường dẫn file ảnh upload
            $table->string('link_anh', 500)->nullable();       // Link ảnh ngoài (URL)
            $table->string('lien_ket', 500)->nullable();       // URL khi click vào banner
            $table->string('vi_tri', 50)->default('hero');     // hero | popup | sidebar
            $table->unsignedTinyInteger('thu_tu')->default(0); // Thứ tự hiển thị
            $table->boolean('trang_thai')->default(true);      // Bật/Tắt
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
        Schema::dropIfExists('banners');
    }
}
