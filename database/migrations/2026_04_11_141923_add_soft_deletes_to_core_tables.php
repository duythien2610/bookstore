<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToCoreTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('sach', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('don_hang', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('the_loai', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('tac_gia', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('nha_xuat_ban', function (Blueprint $table) { $table->softDeletes(); });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('sach', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('don_hang', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('the_loai', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('tac_gia', function (Blueprint $table) { $table->dropSoftDeletes(); });
        Schema::table('nha_xuat_ban', function (Blueprint $table) { $table->dropSoftDeletes(); });
    }
}
