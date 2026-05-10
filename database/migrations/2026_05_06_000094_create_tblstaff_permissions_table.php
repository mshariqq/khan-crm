<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblstaff_permissions', function (Blueprint $table) {
            $table->bigIncrements('staff_id');
            $table->string('feature', 40);
            $table->string('capability', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblstaff_permissions');
    }
};
