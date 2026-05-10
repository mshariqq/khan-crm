<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make lead table columns that have defaults also nullable
     * to handle empty form submissions gracefully
     */
    public function up(): void
    {
        Schema::table('tblleads', function (Blueprint $table) {
            // Columns with integer defaults - make nullable
            $table->integer('country')->default(0)->nullable()->change();
            $table->integer('assigned')->default(0)->nullable()->change();
            $table->integer('from_form_id')->default(0)->nullable()->change();
            $table->integer('leadorder')->default(1)->nullable()->change();
            $table->integer('junk')->default(0)->nullable()->change();
            $table->integer('last_lead_status')->default(0)->nullable()->change();
            $table->integer('client_id')->default(0)->nullable()->change();
            
            // Boolean columns with defaults - make nullable
            $table->boolean('lost')->default(false)->nullable()->change();
            $table->boolean('is_imported_from_email_integration')->default(false)->nullable()->change();
            $table->boolean('is_public')->default(false)->nullable()->change();
        });
    }

    /**
     * Revert columns to NOT NULL
     */
    public function down(): void
    {
        Schema::table('tblleads', function (Blueprint $table) {
            $table->integer('country')->default(0)->nullable(false)->change();
            $table->integer('assigned')->default(0)->nullable(false)->change();
            $table->integer('from_form_id')->default(0)->nullable(false)->change();
            $table->integer('leadorder')->default(1)->nullable(false)->change();
            $table->integer('junk')->default(0)->nullable(false)->change();
            $table->integer('last_lead_status')->default(0)->nullable(false)->change();
            $table->integer('client_id')->default(0)->nullable(false)->change();
            $table->boolean('lost')->default(false)->nullable(false)->change();
            $table->boolean('is_imported_from_email_integration')->default(false)->nullable(false)->change();
            $table->boolean('is_public')->default(false)->nullable(false)->change();
        });
    }
};