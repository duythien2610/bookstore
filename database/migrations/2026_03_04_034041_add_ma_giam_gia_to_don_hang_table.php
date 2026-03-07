<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaGiamGiaToDonHangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('don_hang', function (Blueprint $table) {
            //
            $table->foreignId('ma_giam_gia_id')->nullable()->after('id')
              ->constrained('ma_giam_gia')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('don_hang', function (Blueprint $table) {
            //
            
        });
    }
}
