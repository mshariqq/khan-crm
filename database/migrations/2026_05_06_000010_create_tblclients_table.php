<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblclients', function (Blueprint $table) {
            $table->bigIncrements('userid');
            $table->string('company', 191)->nullable();
            $table->string('vat', 50)->nullable();
            $table->string('phonenumber', 30)->nullable();
            $table->integer('country')->default(0);
            $table->string('city', 100)->nullable();
            $table->string('zip', 15)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('address', 191)->nullable();
            $table->string('website', 150)->nullable();
            $table->dateTime('datecreated');
            $table->integer('active')->default(1);
            $table->integer('leadid')->nullable();
            $table->string('billing_street', 200)->nullable();
            $table->string('billing_city', 100)->nullable();
            $table->string('billing_state', 100)->nullable();
            $table->string('billing_zip', 100)->nullable();
            $table->integer('billing_country')->default(0);
            $table->string('shipping_street', 200)->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->string('shipping_zip', 100)->nullable();
            $table->integer('shipping_country')->default(0);
            $table->string('longitude', 191)->nullable();
            $table->string('latitude', 191)->nullable();
            $table->string('default_language', 40)->nullable();
            $table->integer('default_currency')->default(0);
            $table->integer('customer_group_id')->nullable();
            $table->integer('show_primary_contact')->default(0);
            $table->string('stripe_id', 40)->nullable();
            $table->integer('registration_confirmed')->default(1);
            $table->integer('addedfrom')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblclients');
    }
};
