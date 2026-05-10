<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblmail_queue', function (Blueprint $table) {
            $table->id();
            $table->string('engine', 40)->nullable();
            $table->string('email', 191);
            $table->mediumText('cc')->nullable();
            $table->mediumText('bcc')->nullable();
            $table->longText('message');
            $table->longText('alt_message')->nullable();
            $table->enum('status', ['pending', 'sending', 'sent', 'failed'])->nullable();
            $table->dateTime('date')->nullable();
            $table->mediumText('headers')->nullable();
            $table->longText('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblmail_queue');
    }
};
