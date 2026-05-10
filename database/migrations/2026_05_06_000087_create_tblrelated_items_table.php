<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblrelated_items', function (Blueprint $table) {
            $table->id();
            $table->integer('rel_id');
            $table->string('rel_type', 30);
            $table->integer('item_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblrelated_items');
    }
};
