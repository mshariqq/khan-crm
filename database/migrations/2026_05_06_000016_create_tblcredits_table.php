<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblcredits', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id');
            $table->integer('credit_id');
            $table->integer('staff_id');
            $table->date('date');
            $table->dateTime('date_applied');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblcredits');
    }
};
