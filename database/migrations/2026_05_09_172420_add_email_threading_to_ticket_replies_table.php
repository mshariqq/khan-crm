<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblticket_replies', function (Blueprint $table) {
            $table->string('message_id', 255)->nullable()->after('message');
            $table->string('in_reply_to', 255)->nullable()->after('message_id');
            $table->text('references')->nullable()->after('in_reply_to');
        });
    }

    public function down(): void
    {
        Schema::table('tblticket_replies', function (Blueprint $table) {
            $table->dropColumn(['message_id', 'in_reply_to', 'references']);
        });
    }
};
