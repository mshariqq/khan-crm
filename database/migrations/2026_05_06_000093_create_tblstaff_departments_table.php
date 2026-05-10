<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblstaff_departments', function (Blueprint $table) {
            $table->bigIncrements('staffdepartmentid');
            $table->integer('staffid');
            $table->integer('departmentid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblstaff_departments');
    }
};
