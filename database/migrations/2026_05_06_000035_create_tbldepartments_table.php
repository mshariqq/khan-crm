<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbldepartments', function (Blueprint $table) {
            $table->bigIncrements('departmentid');
            $table->string('name', 100);
            $table->string('imap_username', 191)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('email_from_header')->default(false);
            $table->string('host', 150)->nullable();
            $table->longText('password')->nullable();
            $table->string('encryption', 3)->nullable();
            $table->string('folder', 191)->default('INBOX');
            $table->integer('delete_after_import')->default(0);
            $table->longText('calendar_id')->nullable();
            $table->boolean('hidefromclient')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbldepartments');
    }
};
