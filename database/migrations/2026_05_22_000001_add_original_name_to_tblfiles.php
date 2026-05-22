<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblfiles', function (Blueprint $table) {
            if (!Schema::hasColumn('tblfiles', 'original_file_name')) {
                $table->string('original_file_name', 191)->nullable()->after('file_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblfiles', function (Blueprint $table) {
            if (Schema::hasColumn('tblfiles', 'original_file_name')) {
                $table->dropColumn('original_file_name');
            }
        });
    }
};
