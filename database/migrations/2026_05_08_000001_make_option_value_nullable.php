<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make the value column nullable to allow empty settings
     */
    public function up(): void
    {
        Schema::table('tbloptions', function (Blueprint $table) {
            $table->longText('value')->nullable()->change();
        });
    }

    /**
     * Revert the column to NOT NULL
     */
    public function down(): void
    {
        Schema::table('tbloptions', function (Blueprint $table) {
            $table->longText('value')->nullable(false)->change();
        });
    }
};