<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbltasks', function (Blueprint $t) {
            $t->text('tags')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('tbltasks', function (Blueprint $t) {
            $t->dropColumn('tags');
        });
    }
};
