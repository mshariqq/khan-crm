<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('ticketid');
            $table->integer('replyid')->nullable();
            $table->string('file_name', 191);
            $table->string('filetype', 50)->nullable();
            $table->dateTime('dateadded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblticket_attachments');
    }
};
