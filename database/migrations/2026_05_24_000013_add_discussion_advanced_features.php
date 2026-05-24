<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblproject_discussion_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('color', 20)->default('#6b7280');
            $table->timestamps();
        });

        Schema::table('tblprojectdiscussions', function (Blueprint $table) {
            if (!Schema::hasColumn('tblprojectdiscussions', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('show_to_customer');
            }
            if (!Schema::hasColumn('tblprojectdiscussions', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('is_pinned');
            }
        });

        Schema::table('tblprojectdiscussioncomments', function (Blueprint $table) {
            if (!Schema::hasColumn('tblprojectdiscussioncomments', 'mentions')) {
                $table->text('mentions')->nullable()->after('file_mime_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblprojectdiscussioncomments', function (Blueprint $table) {
            $table->dropColumn('mentions');
        });
        Schema::table('tblprojectdiscussions', function (Blueprint $table) {
            $table->dropColumn(['is_pinned', 'category_id']);
        });
        Schema::dropIfExists('tblproject_discussion_categories');
    }
};
