<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Line items for estimates
     */
    public function up(): void
    {
        Schema::create('tblestimate_items', function (Blueprint $table) {
            $table->id();
            $table->integer('estimate_id')->index();
            $table->integer('item_id')->nullable()->index();
            $table->string('description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('rate', 15, 2)->default(0.00);
            $table->decimal('qty', 15, 2)->default(1.00);
            $table->string('unit', 10)->nullable();
            $table->integer('tax')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblestimate_items');
    }
};