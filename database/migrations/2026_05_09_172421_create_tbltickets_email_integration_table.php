<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltickets_email_integration', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('email', 100);
            $table->string('imap_server', 100);
            $table->string('encryption', 10)->nullable(); // ssl, tls, etc
            $table->integer('port')->default(993);
            $table->longText('password');
            $table->integer('check_every')->default(5); // minutes
            $table->integer('department_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->string('folder', 100)->default('INBOX');
            $table->dateTime('last_run')->nullable();
            $table->boolean('only_loop_on_unseen_emails')->default(true);
            $table->boolean('delete_after_import')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltickets_email_integration');
    }
};
