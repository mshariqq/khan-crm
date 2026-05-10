<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblproject_activity', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->integer('staff_id')->default(0);
            $table->integer('contact_id')->default(0);
            $table->string('fullname', 100)->nullable();
            $table->integer('visible_to_customer')->default(0);
            $table->string('description_key', 191);
            $table->mediumText('additional_data')->nullable();
            $table->dateTime('dateadded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblproject_activity');
    }
};
