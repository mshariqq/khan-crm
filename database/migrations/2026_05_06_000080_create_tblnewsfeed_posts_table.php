<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblnewsfeed_posts', function (Blueprint $table) {
            $table->bigIncrements('postid');
            $table->integer('creator');
            $table->dateTime('datecreated');
            $table->string('visibility', 100);
            $table->mediumText('content');
            $table->integer('pinned');
            $table->dateTime('datepinned')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblnewsfeed_posts');
    }
};
