<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblpinned_projects', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->integer('staff_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblpinned_projects');
    }
};
