<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltickets_predefined_replies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->mediumText('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltickets_predefined_replies');
    }
};
