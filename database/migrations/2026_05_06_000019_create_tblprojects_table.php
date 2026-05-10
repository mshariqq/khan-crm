<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblprojects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->mediumText('description')->nullable();
            $table->integer('status')->default(0);
            $table->integer('clientid');
            $table->integer('billing_type');
            $table->date('start_date');
            $table->date('deadline')->nullable();
            $table->date('project_created');
            $table->dateTime('date_finished')->nullable();
            $table->integer('progress')->default(0);
            $table->integer('progress_from_tasks')->default(1);
            $table->decimal('project_cost', 15, 2)->nullable();
            $table->decimal('project_rate_per_hour', 15, 2)->nullable();
            $table->decimal('estimated_hours', 15, 2)->nullable();
            $table->integer('addedfrom');
            $table->integer('contact_notification')->default(1);
            $table->mediumText('notify_contacts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblprojects');
    }
};
