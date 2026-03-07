<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
             $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->tinyInteger('trang_thai')->default(1)->after('role_id'); // 1=active, 0=banned
            $table->string('remember_token', 100)->nullable()->after('trang_thai');
            $table->string('avatar', 200)->nullable()->after('dia_chi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('email_verified_at');
            $table->dropColumn('trang_thai');
            $table->dropColumn('remember_token');
            $table->dropColumn('avatar');
        });
    }
}
