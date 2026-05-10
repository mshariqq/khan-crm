<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblreminders', function (Blueprint $table) {
            $table->id();
            $table->mediumText('description')->nullable();
            $table->dateTime('date');
            $table->integer('isnotified')->default(0);
            $table->integer('rel_id');
            $table->integer('staff');
            $table->string('rel_type', 40);
            $table->integer('notify_by_email')->default(1);
            $table->integer('creator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblreminders');
    }
};
