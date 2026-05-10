<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbloptions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->longText('value');
            $table->boolean('autoload')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbloptions');
    }
};
