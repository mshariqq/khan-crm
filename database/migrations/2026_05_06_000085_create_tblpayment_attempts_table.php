<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblpayment_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100);
            $table->integer('invoice_id');
            $table->double('amount');
            $table->double('fee');
            $table->string('payment_gateway', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblpayment_attempts');
    }
};
