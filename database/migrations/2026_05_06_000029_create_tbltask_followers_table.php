<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltask_followers', function (Blueprint $table) {
            $table->id();
            $table->integer('staffid');
            $table->integer('taskid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltask_followers');
    }
};
