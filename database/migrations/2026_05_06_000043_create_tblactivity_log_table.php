<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblactivity_log', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->dateTime('date');
            $table->string('staffid', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblactivity_log');
    }
};
