<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltaggables', function (Blueprint $table) {
            $table->integer('rel_id');
            $table->string('rel_type', 20);
            $table->integer('tag_id');
            $table->integer('tag_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltaggables');
    }
};
