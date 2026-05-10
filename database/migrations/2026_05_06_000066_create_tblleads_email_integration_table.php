<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblleads_email_integration', function (Blueprint $table) {
            $table->id();
            $table->integer('active');
            $table->string('email', 100);
            $table->string('imap_server', 100);
            $table->longText('password');
            $table->integer('check_every')->default(5);
            $table->integer('responsible');
            $table->integer('lead_source');
            $table->integer('lead_status');
            $table->string('encryption', 3)->nullable();
            $table->string('folder', 100);
            $table->string('last_run', 50)->nullable();
            $table->boolean('notify_lead_imported')->default(true);
            $table->boolean('notify_lead_contact_more_times')->default(true);
            $table->string('notify_type', 20)->nullable();
            $table->longText('notify_ids')->nullable();
            $table->integer('mark_public')->default(0);
            $table->boolean('only_loop_on_unseen_emails')->default(true);
            $table->integer('delete_after_import')->default(0);
            $table->integer('create_task_if_customer')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblleads_email_integration');
    }
};
