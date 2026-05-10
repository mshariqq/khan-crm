<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblshared_customer_files', function (Blueprint $table) {
            $table->bigIncrements('file_id');
            $table->integer('contact_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblshared_customer_files');
    }
};
