<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblpayment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->mediumText('description')->nullable();
            $table->integer('show_on_pdf')->default(0);
            $table->integer('invoices_only')->default(0);
            $table->integer('expenses_only')->default(0);
            $table->integer('selected_by_default')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblpayment_modes');
    }
};
