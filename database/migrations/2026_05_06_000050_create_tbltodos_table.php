<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltodos', function (Blueprint $table) {
            $table->bigIncrements('todoid');
            $table->mediumText('description');
            $table->integer('staffid');
            $table->dateTime('dateadded');
            $table->boolean('finished');
            $table->dateTime('datefinished')->nullable();
            $table->integer('item_order')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltodos');
    }
};
