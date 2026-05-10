<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tblstaff_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staffid');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status', 50)->default('Present');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->foreign('staffid')->references('staffid')->on('tblstaff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');
    }
};
