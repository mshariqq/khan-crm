<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblleads', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 65)->nullable();
            $table->string('name', 191);
            $table->string('title', 100)->nullable();
            $table->string('company', 191)->nullable();
            $table->mediumText('description')->nullable();
            $table->integer('country')->default(0);
            $table->string('zip', 15)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('address', 100)->nullable();
            $table->integer('assigned')->default(0);
            $table->dateTime('dateadded');
            $table->integer('from_form_id')->default(0);
            $table->integer('status');
            $table->integer('source');
            $table->dateTime('lastcontact')->nullable();
            $table->date('dateassigned')->nullable();
            $table->dateTime('last_status_change')->nullable();
            $table->integer('addedfrom');
            $table->string('email', 100)->nullable();
            $table->string('website', 150)->nullable();
            $table->integer('leadorder')->default(1);
            $table->string('phonenumber', 50)->nullable();
            $table->dateTime('date_converted')->nullable();
            $table->boolean('lost')->default(false);
            $table->integer('junk')->default(0);
            $table->integer('last_lead_status')->default(0);
            $table->boolean('is_imported_from_email_integration')->default(false);
            $table->string('email_integration_uid', 30)->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('default_language', 40)->nullable();
            $table->integer('client_id')->default(0);
            $table->decimal('lead_value', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblleads');
    }
};
