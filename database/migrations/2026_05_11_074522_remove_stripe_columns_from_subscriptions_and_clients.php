<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tblsubscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_tax_id',
                'stripe_tax_id_2',
                'stripe_plan_id',
                'stripe_subscription_id',
                'in_test_environment'
            ]);
        });

        Schema::table('tblclients', function (Blueprint $table) {
            $table->dropColumn('stripe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tblsubscriptions', function (Blueprint $table) {
            $table->string('stripe_tax_id', 50)->nullable();
            $table->string('stripe_tax_id_2', 50)->nullable();
            $table->mediumText('stripe_plan_id')->nullable();
            $table->mediumText('stripe_subscription_id')->nullable();
            $table->integer('in_test_environment')->nullable();
        });

        Schema::table('tblclients', function (Blueprint $table) {
            $table->string('stripe_id', 40)->nullable();
        });
    }
};
