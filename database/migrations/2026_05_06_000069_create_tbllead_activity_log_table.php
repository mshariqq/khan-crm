<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbllead_activity_log', function (Blueprint $table) {
            $table->id();
            $table->integer('leadid');
            $table->longText('description');
            $table->mediumText('additional_data')->nullable();
            $table->dateTime('date');
            $table->integer('staffid');
            $table->string('full_name', 100)->nullable();
            $table->boolean('custom_activity')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbllead_activity_log');
    }
};
