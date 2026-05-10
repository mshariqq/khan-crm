<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblsubscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->mediumText('description')->nullable();
            $table->boolean('description_in_item')->default(false);
            $table->integer('clientid');
            $table->date('date')->nullable();
            $table->mediumText('terms')->nullable();
            $table->integer('currency');
            $table->integer('tax_id')->default(0);
            $table->string('stripe_tax_id', 50)->nullable();
            $table->integer('tax_id_2')->default(0);
            $table->string('stripe_tax_id_2', 50)->nullable();
            $table->mediumText('stripe_plan_id')->nullable();
            $table->mediumText('stripe_subscription_id');
            $table->bigInteger('next_billing_cycle')->nullable();
            $table->bigInteger('ends_at')->nullable();
            $table->string('status', 50)->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('project_id')->default(0);
            $table->string('hash', 32);
            $table->dateTime('created');
            $table->integer('created_from');
            $table->dateTime('date_subscribed')->nullable();
            $table->integer('in_test_environment')->nullable();
            $table->dateTime('last_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblsubscriptions');
    }
};
