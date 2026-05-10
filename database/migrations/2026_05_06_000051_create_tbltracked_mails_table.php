<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltracked_mails', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 32);
            $table->integer('rel_id');
            $table->string('rel_type', 40);
            $table->dateTime('date');
            $table->string('email', 100);
            $table->boolean('opened')->default(false);
            $table->dateTime('date_opened')->nullable();
            $table->longText('subject')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltracked_mails');
    }
};
