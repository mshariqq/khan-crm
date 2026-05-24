<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tblproject_file_folders')) {
            Schema::create('tblproject_file_folders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->string('name', 191);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('tblproject_file_downloads')) {
            Schema::create('tblproject_file_downloads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('file_id');
                $table->unsignedBigInteger('staffid')->nullable();
                $table->unsignedBigInteger('contact_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamp('downloaded_at');
            });
        }

        Schema::table('tblproject_files', function (Blueprint $table) {
            if (!Schema::hasColumn('tblproject_files', 'folder_id')) {
                $table->unsignedBigInteger('folder_id')->nullable()->after('project_id');
            }
            if (!Schema::hasColumn('tblproject_files', 'file_version')) {
                $table->integer('file_version')->default(1)->after('file_size');
            }
            if (!Schema::hasColumn('tblproject_files', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('file_version');
            }
            if (!Schema::hasColumn('tblproject_files', 'virus_scan_status')) {
                $table->string('virus_scan_status', 20)->default('pending')->after('expires_at');
            }
            if (!Schema::hasColumn('tblproject_files', 'virus_scan_at')) {
                $table->timestamp('virus_scan_at')->nullable()->after('virus_scan_status');
            }
            if (!Schema::hasColumn('tblproject_files', 'scan_result')) {
                $table->text('scan_result')->nullable()->after('virus_scan_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblproject_files', function (Blueprint $table) {
            $table->dropColumn(['folder_id', 'file_version', 'expires_at', 'virus_scan_status', 'virus_scan_at', 'scan_result']);
        });
        Schema::dropIfExists('tblproject_file_downloads');
        Schema::dropIfExists('tblproject_file_folders');
    }
};
