<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblconsents', function (Blueprint $table) {
            $table->id();
            $table->string('action', 10);
            $table->dateTime('date');
            $table->string('ip', 40);
            $table->integer('contact_id')->default(0);
            $table->integer('lead_id')->default(0);
            $table->mediumText('description')->nullable();
            $table->mediumText('opt_in_purpose_description')->nullable();
            $table->integer('purpose_id');
            $table->string('staff_name', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblconsents');
    }
};
