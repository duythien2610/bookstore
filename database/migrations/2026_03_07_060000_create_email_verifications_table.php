<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailVerificationsTable extends Migration
{
    public function up()
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('code_hash');       // Mã 6 số đã hash (không lưu plain text)
            $table->timestamp('expires_at');    // Hết hạn sau 10 phút
            $table->unsignedTinyInteger('attempts')->default(0); // Số lần thử sai
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_verifications');
    }
}
