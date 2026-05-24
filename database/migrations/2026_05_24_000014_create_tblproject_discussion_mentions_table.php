<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tblproject_discussion_mentions')) {
            Schema::create('tblproject_discussion_mentions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('discussion_id');
                $table->unsignedBigInteger('comment_id')->nullable();
                $table->unsignedBigInteger('mentioned_staff_id');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('tblprojectdiscussioncomments', function (Blueprint $table) {
            if (!Schema::hasColumn('tblprojectdiscussioncomments', 'original_file_name')) {
                $table->string('original_file_name', 191)->nullable()->after('file_mime_type');
            }
            if (!Schema::hasColumn('tblprojectdiscussioncomments', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('original_file_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblprojectdiscussioncomments', function (Blueprint $table) {
            $table->dropColumn(['original_file_name', 'file_size']);
        });
        Schema::dropIfExists('tblproject_discussion_mentions');
    }
};
