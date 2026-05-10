<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbltickets', function (Blueprint $table) {
            $table->string('message_id', 255)->nullable()->after('subject');
            $table->text('references')->nullable()->after('message_id');
        });
    }

    public function down(): void
    {
        Schema::table('tbltickets', function (Blueprint $table) {
            $table->dropColumn(['message_id', 'references']);
        });
    }
};
