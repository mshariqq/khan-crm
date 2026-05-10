<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblprojectdiscussioncomments', function (Blueprint $table) {
            $table->id();
            $table->integer('discussion_id');
            $table->string('discussion_type', 10);
            $table->integer('parent')->nullable();
            $table->dateTime('created');
            $table->dateTime('modified')->nullable();
            $table->mediumText('content');
            $table->integer('staff_id');
            $table->integer('contact_id')->default(0);
            $table->string('fullname', 191)->nullable();
            $table->string('file_name', 191)->nullable();
            $table->string('file_mime_type', 70)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblprojectdiscussioncomments');
    }
};
