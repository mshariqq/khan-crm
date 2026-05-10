<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblfilter_defaults', function (Blueprint $table) {
            $table->unsignedBigInteger('filter_id');
            $table->unsignedBigInteger('staff_id');
            $table->string('identifier', 191);
            $table->string('view', 191);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblfilter_defaults');
    }
};
