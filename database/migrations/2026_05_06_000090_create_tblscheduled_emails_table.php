<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblscheduled_emails', function (Blueprint $table) {
            $table->id();
            $table->integer('rel_id');
            $table->string('rel_type', 15);
            $table->dateTime('scheduled_at');
            $table->string('contacts', 197);
            $table->mediumText('cc')->nullable();
            $table->boolean('attach_pdf')->default(true);
            $table->string('template', 197);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblscheduled_emails');
    }
};
