<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblvault', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->string('server_address', 191);
            $table->integer('port')->nullable();
            $table->string('username', 191);
            $table->mediumText('password');
            $table->mediumText('description')->nullable();
            $table->integer('creator');
            $table->string('creator_name', 100)->nullable();
            $table->boolean('visibility')->default(true);
            $table->boolean('share_in_projects')->default(false);
            $table->dateTime('last_updated')->nullable();
            $table->string('last_updated_from', 100)->nullable();
            $table->dateTime('date_created');
        $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblvault');
    }
};
