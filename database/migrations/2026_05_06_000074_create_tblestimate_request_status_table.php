<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblestimate_request_status', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('statusorder')->nullable();
            $table->string('color', 10)->nullable();
            $table->string('flag', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblestimate_request_status');
    }
};
