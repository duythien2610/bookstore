<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSachTable extends Migration
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
            $table->integer('so_luong_ton')->default(0)->after('gia_ban');
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
            $table->dropColumn('so_luong_ton');
        });
    }
}
