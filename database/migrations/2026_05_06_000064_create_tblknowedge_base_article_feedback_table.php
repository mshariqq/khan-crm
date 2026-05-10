<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblknowledge_base_article_feedback', function (Blueprint $table) {
            $table->id();
            $table->integer('articleid');
            $table->integer('answer');
            $table->string('ip', 40);
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblknowledge_base_article_feedback');
    }
};
