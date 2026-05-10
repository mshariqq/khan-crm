<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltask_comments', function (Blueprint $table) {
            $table->id();
            $table->mediumText('content');
            $table->integer('taskid');
            $table->integer('staffid');
            $table->integer('contact_id')->default(0);
            $table->integer('file_id')->default(0);
            $table->dateTime('dateadded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltask_comments');
    }
};
