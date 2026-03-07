<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->index();
            $table->string('code_hash');       // Mã 6 số đã hash
            $table->timestamp('expires_at');    // Hết hạn sau 10 phút
            $table->unsignedTinyInteger('attempts')->default(0); // Số lần thử sai
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
}
