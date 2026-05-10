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
        Schema::create('tblstaff_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staffid');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('Pending'); // Pending, Approved, Rejected
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('staffid')->references('staffid')->on('tblstaff')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('tblleave_types')->onDelete('cascade');
            $table->foreign('approved_by')->references('staffid')->on('tblstaff')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_leaves');
    }
};
