<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbluser_meta', function (Blueprint $table) {
            $table->unsignedBigInteger('umeta_id')->primary();
            $table->unsignedBigInteger('staff_id')->default(0);
            $table->unsignedBigInteger('client_id')->default(0);
            $table->unsignedBigInteger('contact_id')->default(0);
            $table->string('meta_key', 191)->nullable();
            $table->longText('meta_value')->nullable();
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbluser_meta');
    }
};
