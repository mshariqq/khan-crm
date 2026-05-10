<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblknowledge_base', function (Blueprint $table) {
            $table->bigIncrements('articleid');
            $table->integer('articlegroup');
            $table->longText('subject');
            $table->mediumText('description');
            $table->longText('slug');
            $table->boolean('active');
            $table->dateTime('datecreated');
            $table->integer('article_order')->default(0);
            $table->integer('staff_article')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblknowledge_base');
    }
};
