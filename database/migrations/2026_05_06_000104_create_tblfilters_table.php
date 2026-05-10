<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblfilters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->mediumText('builder');
            $table->unsignedBigInteger('staff_id');
            $table->string('identifier', 191);
            $table->unsignedTinyInteger('is_shared')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblfilters');
    }
};
