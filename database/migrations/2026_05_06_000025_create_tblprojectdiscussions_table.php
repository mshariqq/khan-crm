<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblprojectdiscussions', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->string('subject', 191);
            $table->mediumText('description');
            $table->boolean('show_to_customer')->default(false);
            $table->dateTime('datecreated');
            $table->dateTime('last_activity')->nullable();
            $table->integer('staff_id')->default(0);
            $table->integer('contact_id')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblprojectdiscussions');
    }
};
