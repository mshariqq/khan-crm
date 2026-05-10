<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblnotifications', function (Blueprint $table) {
            $table->id();
            $table->integer('isread')->default(0);
            $table->boolean('isread_inline')->default(false);
            $table->dateTime('date');
            $table->mediumText('description');
            $table->integer('fromuserid');
            $table->integer('fromclientid')->default(0);
            $table->string('from_fullname', 100);
            $table->integer('touserid');
            $table->integer('fromcompany')->nullable();
            $table->longText('link')->nullable();
            $table->mediumText('additional_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblnotifications');
    }
};
