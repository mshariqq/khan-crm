<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblnewsfeed_post_comments', function (Blueprint $table) {
            $table->id();
            $table->mediumText('content')->nullable();
            $table->integer('userid');
            $table->integer('postid');
            $table->dateTime('dateadded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblnewsfeed_post_comments');
    }
};
