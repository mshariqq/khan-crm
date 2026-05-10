<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblproject_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_name', 191);
            $table->longText('original_file_name')->nullable();
            $table->string('subject', 191)->nullable();
            $table->mediumText('description')->nullable();
            $table->string('filetype', 50)->nullable();
            $table->dateTime('dateadded');
            $table->dateTime('last_activity')->nullable();
            $table->integer('project_id');
            $table->boolean('visible_to_customer')->default(false);
            $table->integer('staffid');
            $table->integer('contact_id')->default(0);
            $table->string('external', 40)->nullable();
            $table->mediumText('external_link')->nullable();
            $table->mediumText('thumbnail_link')->nullable();
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblproject_files');
    }
};
