<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblannouncements', function (Blueprint $table) {
            $table->bigIncrements('announcementid');
            $table->string('name', 191);
            $table->mediumText('message')->nullable();
            $table->integer('showtousers');
            $table->integer('showtostaff');
            $table->integer('showname');
            $table->dateTime('dateadded');
            $table->string('userid', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblannouncements');
    }
};
