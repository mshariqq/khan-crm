<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblitem_tax', function (Blueprint $table) {
            $table->id();
            $table->integer('itemid');
            $table->integer('rel_id');
            $table->string('rel_type', 20);
            $table->decimal('taxrate', 15, 2);
            $table->string('taxname', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblitem_tax');
    }
};
