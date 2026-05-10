<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbluser_auto_login', function (Blueprint $table) {
            $table->string('key_id', 64)->primary();
            $table->integer('user_id');
            $table->string('user_agent', 150);
            $table->string('last_ip', 40);
            $table->timestamp('last_login')->useCurrent()->useCurrentOnUpdate();
            $table->integer('staff');
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbluser_auto_login');
    }
};
