<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblitems', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->mediumText('long_description')->nullable();
            $table->decimal('rate', 15, 2);
            $table->integer('tax')->nullable();
            $table->integer('tax2')->nullable();
            $table->string('unit', 40)->nullable();
            $table->integer('group_id')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblitems');
    }
};
