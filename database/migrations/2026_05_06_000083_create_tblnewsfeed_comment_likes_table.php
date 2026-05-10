<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblnewsfeed_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->integer('postid');
            $table->integer('commentid');
            $table->integer('userid');
            $table->dateTime('dateliked');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblnewsfeed_comment_likes');
    }
};
