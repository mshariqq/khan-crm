<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblviews_tracking', function (Blueprint $table) {
            $table->id();
            $table->integer('rel_id');
            $table->string('rel_type', 40);
            $table->dateTime('date');
            $table->string('view_ip', 40);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblviews_tracking');
    }
};
