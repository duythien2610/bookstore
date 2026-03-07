<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentToTheLoaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('the_loai', function (Blueprint $table) {
            //
            $table->foreignId('parent_id')
              ->nullable()
              ->after('id')
              ->constrained('the_loai')
              ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('the_loai', function (Blueprint $table) {
            //
            $table->dropForeign(['parent_id']);
        $table->dropColumn('parent_id');
        });
    }
}
