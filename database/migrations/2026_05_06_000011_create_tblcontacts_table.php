<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblcontacts', function (Blueprint $table) {
            $table->id();
            $table->integer('userid');
            $table->integer('is_primary')->default(1);
            $table->string('firstname', 191);
            $table->string('lastname', 191);
            $table->string('email', 100);
            $table->string('phonenumber', 100);
            $table->string('title', 100)->nullable();
            $table->dateTime('datecreated');
            $table->string('password', 255)->nullable();
            $table->string('new_pass_key', 32)->nullable();
            $table->dateTime('new_pass_key_requested')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('email_verification_key', 32)->nullable();
            $table->dateTime('email_verification_sent_at')->nullable();
            $table->string('last_ip', 40)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_password_change')->nullable();
            $table->boolean('active')->default(true);
            $table->string('profile_image', 191)->nullable();
            $table->string('direction', 3)->nullable();
            $table->boolean('invoice_emails')->default(true);
            $table->boolean('estimate_emails')->default(true);
            $table->boolean('credit_note_emails')->default(true);
            $table->boolean('contract_emails')->default(true);
            $table->boolean('task_emails')->default(true);
            $table->boolean('project_emails')->default(true);
            $table->boolean('ticket_emails')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblcontacts');
    }
};
