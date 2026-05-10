<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblinvoicepaymentrecords', function (Blueprint $table) {
            $table->id();
            $table->integer('invoiceid');
            $table->decimal('amount', 15, 2);
            $table->string('paymentmode', 40)->nullable();
            $table->string('paymentmethod', 191)->nullable();
            $table->date('date');
            $table->dateTime('daterecorded');
            $table->mediumText('note')->nullable();
            $table->longText('transactionid')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblinvoicepaymentrecords');
    }
};
