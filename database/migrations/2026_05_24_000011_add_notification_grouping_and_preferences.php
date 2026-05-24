<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblnotifications', function (Blueprint $table) {
            if (!Schema::hasColumn('tblnotifications', 'notification_type')) {
                $table->string('notification_type', 50)->nullable()->after('additional_data');
            }
            if (!Schema::hasColumn('tblnotifications', 'grouping_key')) {
                $table->string('grouping_key', 100)->nullable()->after('notification_type');
            }
            if (!Schema::hasColumn('tblnotifications', 'group_count')) {
                $table->integer('group_count')->default(1)->after('grouping_key');
            }
        });

        try {
            Schema::table('tblnotifications', function (Blueprint $table) {
                $table->index('notification_type', 'tblnotifications_notification_type_index');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('tblnotifications', function (Blueprint $table) {
                $table->index('grouping_key', 'tblnotifications_grouping_key_index');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('tblnotifications', function (Blueprint $table) {
                $table->index(['touserid', 'isread', 'created_at'], 'tblnotifications_touserid_isread_created_at_index');
            });
        } catch (\Exception $e) {}

        if (!Schema::hasTable('tblstaff_notification_preferences')) {
            Schema::create('tblstaff_notification_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staffid');
                $table->string('notification_type', 50);
                $table->boolean('in_app')->default(true);
                $table->boolean('email')->default(true);
                $table->timestamps();

                $table->unique(['staffid', 'notification_type'], 'staff_notif_pref_unique');
                $table->foreign('staffid', 'staff_notif_pref_fk')->references('staffid')->on('tblstaff')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('tblemail_tracking')) {
            Schema::create('tblemail_tracking', function (Blueprint $table) {
                $table->id();
                $table->string('message_id', 255)->unique('email_tracking_msg_id_unique');
                $table->string('recipient_email', 255);
                $table->string('subject', 255)->nullable();
                $table->string('slug', 100)->nullable();
                $table->nullableMorphs('trackable');
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->integer('open_count')->default(0);
                $table->integer('click_count')->default(0);
                $table->text('user_agent')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('status', 50)->default('sent');
                $table->text('bounce_reason')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tblemail_click_tracking')) {
            Schema::create('tblemail_click_tracking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('email_tracking_id');
                $table->text('url');
                $table->timestamp('clicked_at')->nullable();
                $table->timestamps();

                $table->foreign('email_tracking_id', 'email_click_tracking_fk')->references('id')->on('tblemail_tracking')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('tblemail_rate_limits')) {
            Schema::create('tblemail_rate_limits', function (Blueprint $table) {
                $table->id();
                $table->nullableMorphs('limitable');
                $table->string('recipient_type', 50)->default('client');
                $table->integer('emails_sent')->default(0);
                $table->integer('max_per_hour')->default(50);
                $table->timestamp('hour_bucket');
                $table->timestamps();

                $table->index(['limitable_type', 'limitable_id', 'hour_bucket'], 'email_rate_limitable_bucket_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tblemail_rate_limits');
        Schema::dropIfExists('tblemail_click_tracking');
        Schema::dropIfExists('tblemail_tracking');

        Schema::dropIfExists('tblstaff_notification_preferences');

        Schema::table('tblnotifications', function (Blueprint $table) {
            $table->dropIndex('tblnotifications_touserid_isread_created_at_index');
            $table->dropIndex('tblnotifications_grouping_key_index');
            $table->dropIndex('tblnotifications_notification_type_index');
            $table->dropColumn('group_count');
            $table->dropColumn('grouping_key');
            $table->dropColumn('notification_type');
        });
    }
};
