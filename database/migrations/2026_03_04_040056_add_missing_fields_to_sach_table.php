<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToSachTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sach', function (Blueprint $table) {
            //
            $table->decimal('gia_goc', 12, 2)->nullable()->after('gia_ban');
        $table->string('isbn', 20)->nullable()->unique()->after('tieu_de');
        $table->year('nam_xuat_ban')->nullable()->after('isbn');
        $table->integer('so_trang')->nullable()->after('nam_xuat_ban');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sach', function (Blueprint $table) {
            //
            $table->dropColumn('gia_goc');
            $table->dropColumn('isbn');
            $table->dropColumn('nam_xuat_ban');
            $table->dropColumn('so_trang');
        });
    }
}
