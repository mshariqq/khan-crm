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
        Schema::create('tblstaff_salary_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staffid');
            $table->decimal('base_salary', 15, 2)->default(0.00);
            $table->decimal('fixed_allowances', 15, 2)->default(0.00);
            $table->decimal('fixed_deductions', 15, 2)->default(0.00);
            $table->date('effective_date')->default(now());
            $table->timestamps();

            $table->foreign('staffid')->references('staffid')->on('tblstaff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salary_configs');
    }
};
