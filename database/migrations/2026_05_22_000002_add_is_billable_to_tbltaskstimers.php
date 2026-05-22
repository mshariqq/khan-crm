<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbltaskstimers', function (Blueprint $table) {
            if (!Schema::hasColumn('tbltaskstimers', 'is_billable')) {
                $table->boolean('is_billable')->default(false)->after('hourly_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbltaskstimers', function (Blueprint $table) {
            if (Schema::hasColumn('tbltaskstimers', 'is_billable')) {
                $table->dropColumn('is_billable');
            }
        });
    }
};
