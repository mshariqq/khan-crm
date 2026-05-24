<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblstaff', function (Blueprint $table) {
            $table->dateTime('email_verified_at')->nullable()->after('email');
            $table->string('email_verification_key', 32)->nullable()->after('email_verified_at');
            $table->dateTime('email_verification_sent_at')->nullable()->after('email_verification_key');
        });
    }

    public function down(): void
    {
        Schema::table('tblstaff', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'email_verification_key', 'email_verification_sent_at']);
        });
    }
};
