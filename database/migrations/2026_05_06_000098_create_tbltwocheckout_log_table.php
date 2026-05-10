<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltwocheckout_log', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 64);
            $table->unsignedBigInteger('invoice_id');
            $table->string('amount', 25);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltwocheckout_log');
    }
};
