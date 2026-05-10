<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblticket_replies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ticketid');
            $table->integer('userid')->nullable();
            $table->integer('contactid')->default(0);
            $table->mediumText('name')->nullable();
            $table->mediumText('email')->nullable();
            $table->dateTime('date');
            $table->mediumText('message')->nullable();
            $table->integer('attachment')->nullable();
            $table->integer('admin')->nullable();
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblticket_replies');
    }
};
