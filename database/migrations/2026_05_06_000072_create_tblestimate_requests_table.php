<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblestimate_requests', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->longText('submission');
            $table->dateTime('last_status_change')->nullable();
            $table->dateTime('date_estimated')->nullable();
            $table->integer('from_form_id')->nullable();
            $table->integer('assigned')->nullable();
            $table->integer('status')->nullable();
            $table->integer('default_language');
            $table->dateTime('date_added');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblestimate_requests');
    }
};
