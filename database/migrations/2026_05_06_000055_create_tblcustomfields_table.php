<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblcustomfields', function (Blueprint $table) {
            $table->id();
            $table->string('fieldto', 30)->nullable();
            $table->string('name', 150);
            $table->string('slug', 150);
            $table->boolean('required')->default(false);
            $table->string('type', 20);
            $table->longText('options')->nullable();
            $table->boolean('display_inline')->default(false);
            $table->integer('field_order')->default(0);
            $table->integer('active')->default(1);
            $table->integer('show_on_pdf')->default(0);
            $table->boolean('show_on_ticket_form')->default(false);
            $table->boolean('only_admin')->default(false);
            $table->boolean('show_on_table')->default(false);
            $table->integer('show_on_client_portal')->default(0);
            $table->integer('disalow_client_to_edit')->default(0);
            $table->integer('bs_column')->default(12);
            $table->mediumText('default_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblcustomfields');
    }
};
