<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblmilestones', function (Blueprint $table) {
            // Change id to auto‑incrementing big integer primary key
            $table->bigIncrements('id')->change();
            // Make datecreated nullable (existing rows may have null)
            $table->date('datecreated')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tblmilestones', function (Blueprint $table) {
            $table->integer('id')->primary()->change();
            $table->date('datecreated')->change();
        });
    }
};
