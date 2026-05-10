<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblspam_filters', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->string('rel_type', 10);
            $table->mediumText('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblspam_filters');
    }
};
