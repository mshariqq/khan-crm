<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblproject_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->string('title', 255)->nullable();
            $table->mediumText('content');
            $table->integer('staff_id');
            $table->dateTime('dateadded')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblproject_notes');
    }
};
