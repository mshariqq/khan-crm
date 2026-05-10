<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblitemable', function (Blueprint $table) {
            $table->id();
            $table->integer('rel_id');
            $table->string('rel_type', 15);
            $table->longText('description');
            $table->longText('long_description')->nullable();
            $table->decimal('qty', 15, 2);
            $table->decimal('rate', 15, 2);
            $table->string('unit', 40)->nullable();
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_selected')->default(true);
            $table->integer('item_order')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblitemable');
    }
};
