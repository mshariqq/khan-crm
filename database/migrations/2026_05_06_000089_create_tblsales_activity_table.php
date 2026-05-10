<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblsales_activity', function (Blueprint $table) {
            $table->id();
            $table->string('rel_type', 20)->nullable();
            $table->integer('rel_id');
            $table->mediumText('description');
            $table->mediumText('additional_data')->nullable();
            $table->string('staffid', 11)->nullable();
            $table->string('full_name', 100)->nullable();
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblsales_activity');
    }
};
