<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblroles', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('permissions');
        });

        DB::table('tblroles')->where('name', 'Administrator')->orWhere('name', 'Admin')->update(['is_admin' => true]);
    }

    public function down(): void
    {
        Schema::table('tblroles', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
