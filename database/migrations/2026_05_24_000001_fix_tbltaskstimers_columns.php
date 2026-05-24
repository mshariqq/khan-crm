<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbltaskstimers', function (Blueprint $table) {
            $table->dateTime('start_time')->change();
            $table->dateTime('end_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tbltaskstimers', function (Blueprint $table) {
            $table->string('start_time', 64)->change();
            $table->string('end_time', 64)->nullable()->change();
        });
    }
};
