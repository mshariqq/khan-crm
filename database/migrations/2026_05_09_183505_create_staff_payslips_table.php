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
        Schema::create('tblstaff_payslips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staffid');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('basic', 15, 2);
            $table->decimal('total_additions', 15, 2)->default(0.00);
            $table->decimal('total_deductions', 15, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2);
            $table->string('status', 20)->default('Draft'); // Draft, Paid
            $table->date('payment_date')->nullable();
            $table->timestamps();

            $table->foreign('staffid')->references('staffid')->on('tblstaff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_payslips');
    }
};
