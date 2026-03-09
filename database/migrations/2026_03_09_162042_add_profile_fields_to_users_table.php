<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('ngay_sinh')->nullable()->after('so_dien_thoai');
            $table->string('gioi_tinh', 10)->nullable()->after('ngay_sinh'); // male, female, other
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ngay_sinh', 'gioi_tinh']);
        });
    }
}
