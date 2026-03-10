<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoaiSachToSachTable extends Migration
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
            $table->enum('loai_sach', ['trong_nuoc', 'nuoc_ngoai'])
              ->default('trong_nuoc')
              ->after('the_loai_id');
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
             $table->dropColumn('loai_sach');
        });
    }
}
