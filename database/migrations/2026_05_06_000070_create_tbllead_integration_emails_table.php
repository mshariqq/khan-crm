<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbllead_integration_emails', function (Blueprint $table) {
            $table->id();
            $table->longText('subject')->nullable();
            $table->longText('body')->nullable();
            $table->dateTime('dateadded');
            $table->integer('leadid');
            $table->integer('emailid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbllead_integration_emails');
    }
};
