<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltickets', function (Blueprint $table) {
            $table->bigIncrements('ticketid');
            $table->integer('adminreplying')->default(0);
            $table->integer('userid');
            $table->integer('contactid')->default(0);
            $table->integer('merged_ticket_id')->nullable();
            $table->mediumText('email')->nullable();
            $table->mediumText('name')->nullable();
            $table->integer('department');
            $table->integer('priority');
            $table->integer('status');
            $table->integer('service')->nullable();
            $table->string('ticketkey', 32);
            $table->string('subject', 191);
            $table->mediumText('message')->nullable();
            $table->integer('admin')->nullable();
            $table->dateTime('date');
            $table->integer('project_id')->default(0);
            $table->dateTime('lastreply')->nullable();
            $table->integer('clientread')->default(0);
            $table->integer('adminread')->default(0);
            $table->integer('assigned')->default(0);
            $table->integer('staff_id_replying')->nullable();
            $table->string('cc', 191)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltickets');
    }
};
