<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbltasks', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->mediumText('description')->nullable();
            $table->integer('priority')->nullable();
            $table->dateTime('dateadded');
            $table->date('startdate');
            $table->date('duedate')->nullable();
            $table->dateTime('datefinished')->nullable();
            $table->integer('addedfrom');
            $table->boolean('is_added_from_contact')->default(false);
            $table->integer('status')->default(0);
            $table->string('recurring_type', 10)->nullable();
            $table->integer('repeat_every')->nullable();
            $table->integer('recurring')->default(0);
            $table->integer('is_recurring_from')->nullable();
            $table->integer('cycles')->default(0);
            $table->integer('total_cycles')->default(0);
            $table->boolean('custom_recurring')->default(false);
            $table->date('last_recurring_date')->nullable();
            $table->integer('rel_id')->nullable();
            $table->string('rel_type', 30)->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('billable')->default(false);
            $table->boolean('billed')->default(false);
            $table->integer('invoice_id')->default(0);
            $table->decimal('hourly_rate', 15, 2)->default(0.00);
            $table->integer('milestone')->default(0);
            $table->integer('kanban_order')->default(1);
            $table->integer('milestone_order')->default(0);
            $table->boolean('visible_to_client')->default(false);
            $table->integer('deadline_notified')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbltasks');
    }
};
