<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblconsent_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->mediumText('description')->nullable();
            $table->dateTime('date_created');
            $table->dateTime('last_updated')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblconsent_purposes');
    }
};
