<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblnotes', function (Blueprint $table) {
            $table->id();
            $table->integer('rel_id');
            $table->string('rel_type', 20);
            $table->mediumText('description')->nullable();
            $table->dateTime('date_contacted')->nullable();
            $table->integer('addedfrom');
            $table->dateTime('dateadded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblnotes');
    }
};
