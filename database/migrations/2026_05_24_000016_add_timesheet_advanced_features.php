<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbltaskstimers', function (Blueprint $table) {
            if (!Schema::hasColumn('tbltaskstimers', 'status')) {
                $table->string('status', 20)->default('pending')->after('note');
            }
            if (!Schema::hasColumn('tbltaskstimers', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tbltaskstimers', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('tbltaskstimers', 'rounding_minutes')) {
                $table->integer('rounding_minutes')->default(0)->after('approved_at');
            }
        });

        if (!Schema::hasTable('tbltimesheet_audit_log')) {
            Schema::create('tbltimesheet_audit_log', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('timer_id')->nullable();
                $table->unsignedBigInteger('staff_id');
                $table->string('action', 20); // created, updated, deleted, approved, rejected
                $table->text('changes')->nullable(); // JSON diff
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltimesheet_audit_log');
        Schema::table('tbltaskstimers', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'rounding_minutes']);
        });
    }
};
