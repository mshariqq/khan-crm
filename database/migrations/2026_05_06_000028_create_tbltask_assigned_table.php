<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltask_assigned', function (Blueprint $table) {
            $table->id();
            $table->integer('staffid');
            $table->integer('taskid');
            $table->integer('assigned_from')->default(0);
            $table->boolean('is_assigned_from_contact')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltask_assigned');
    }
};
