<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblmilestones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->mediumText('description')->nullable();
            $table->boolean('description_visible_to_customer')->default(false);
            $table->date('start_date')->nullable();
            $table->date('due_date');
            $table->integer('project_id');
            $table->string('color', 10)->nullable();
            $table->integer('milestone_order')->default(0);
            $table->date('datecreated')->nullable();
            $table->integer('hide_from_customer')->default(0);
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblmilestones');
    }
};
