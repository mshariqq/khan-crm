<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblcreditnote_refunds', function (Blueprint $table) {
            $table->id();
            $table->integer('credit_note_id');
            $table->integer('staff_id');
            $table->date('refunded_on');
            $table->string('payment_mode', 40);
            $table->mediumText('note')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblcreditnote_refunds');
    }
};
