<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblknowledge_base_groups', function (Blueprint $table) {
            $table->bigIncrements('groupid');
            $table->string('name', 191);
            $table->mediumText('group_slug')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('active');
            $table->string('color', 10)->default('#28B8DA');
            $table->integer('group_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblknowledge_base_groups');
    }
};
