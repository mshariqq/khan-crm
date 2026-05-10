<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblestimates', function (Blueprint $table) {
            $table->id();
            $table->boolean('sent')->default(false);
            $table->dateTime('datesend')->nullable();
            $table->integer('clientid');
            $table->string('deleted_customer_name', 100)->nullable();
            $table->integer('project_id')->default(0);
            $table->integer('number');
            $table->string('prefix', 50)->nullable();
            $table->integer('number_format')->default(0);
            $table->string('formatted_number', 100)->nullable();
            $table->string('hash', 32)->nullable();
            $table->dateTime('datecreated');
            $table->date('date');
            $table->date('expirydate')->nullable();
            $table->integer('currency');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total_tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2);
            $table->decimal('adjustment', 15, 2)->nullable();
            $table->integer('addedfrom');
            $table->integer('status')->default(1);
            $table->mediumText('clientnote')->nullable();
            $table->mediumText('adminnote')->nullable();
            $table->decimal('discount_percent', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->string('discount_type', 30)->nullable();
            $table->integer('invoiceid')->nullable();
            $table->dateTime('invoiced_date')->nullable();
            $table->mediumText('terms')->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->integer('sale_agent')->default(0);
            $table->string('billing_street', 200)->nullable();
            $table->string('billing_city', 100)->nullable();
            $table->string('billing_state', 100)->nullable();
            $table->string('billing_zip', 100)->nullable();
            $table->integer('billing_country')->nullable();
            $table->string('shipping_street', 200)->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->string('shipping_zip', 100)->nullable();
            $table->integer('shipping_country')->nullable();
            $table->boolean('include_shipping');
            $table->boolean('show_shipping_on_estimate')->default(true);
            $table->integer('show_quantity_as')->default(1);
            $table->integer('pipeline_order')->default(1);
            $table->integer('is_expiry_notified')->default(0);
            $table->string('acceptance_firstname', 50)->nullable();
            $table->string('acceptance_lastname', 50)->nullable();
            $table->string('acceptance_email', 100)->nullable();
            $table->dateTime('acceptance_date')->nullable();
            $table->string('acceptance_ip', 40)->nullable();
            $table->string('signature', 40)->nullable();
            $table->string('short_link', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblestimates');
    }
};
