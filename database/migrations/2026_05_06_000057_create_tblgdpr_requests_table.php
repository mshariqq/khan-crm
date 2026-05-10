<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblgdpr_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('clientid')->default(0);
            $table->integer('contact_id')->default(0);
            $table->integer('lead_id')->default(0);
            $table->string('request_type', 191)->nullable();
            $table->string('status', 40)->nullable();
            $table->dateTime('request_date');
            $table->string('request_from', 150)->nullable();
            $table->mediumText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblgdpr_requests');
    }
};
