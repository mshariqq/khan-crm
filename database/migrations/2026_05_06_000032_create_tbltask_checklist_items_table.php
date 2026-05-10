<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltask_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->integer('taskid');
            $table->mediumText('description');
            $table->integer('finished')->default(0);
            $table->dateTime('dateadded');
            $table->integer('addedfrom');
            $table->integer('finished_from')->nullable();
            $table->integer('list_order')->default(0);
            $table->integer('assigned')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltask_checklist_items');
    }
};
