<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblemailtemplates', function (Blueprint $table) {
            $table->bigIncrements('emailtemplateid');
            $table->longText('type');
            $table->string('slug', 100);
            $table->string('language', 40)->nullable();
            $table->longText('name');
            $table->longText('subject');
            $table->longText('message');
            $table->longText('fromname');
            $table->string('fromemail', 100)->nullable();
            $table->integer('plaintext')->default(0);
            $table->tinyInteger('active')->default(0);
            $table->integer('order');
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblemailtemplates');
    }
};
