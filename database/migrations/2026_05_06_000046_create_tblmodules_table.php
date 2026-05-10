<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblmodules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name', 55);
            $table->string('installed_version', 11);
            $table->boolean('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblmodules');
    }
};
