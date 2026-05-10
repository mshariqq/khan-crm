<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // tblinvoicepaymentrecords - daterecorded should be nullable or default
        try {
            Schema::table('tblinvoicepaymentrecords', function (Blueprint $table) {
                $table->dateTime('daterecorded')->nullable()->change();
            });
        } catch (\Exception $e) {
            // Already done or error
        }

        // tblinvoices - discount_type, include_shipping, show_quantity_as, sale_agent
        try {
            Schema::table('tblinvoices', function (Blueprint $table) {
                $table->string('discount_type', 30)->default('percent')->change();
                $table->boolean('include_shipping')->default(false)->change();
                $table->integer('show_quantity_as')->default(1)->change();
                $table->integer('sale_agent')->default(0)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblestimates - include_shipping, show_quantity_as, sale_agent
        try {
            Schema::table('tblestimates', function (Blueprint $table) {
                $table->boolean('include_shipping')->default(false)->change();
                $table->integer('show_quantity_as')->default(1)->change();
                $table->integer('sale_agent')->default(0)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblproposals - discount_percent, discount_total, show_quantity_as, status
        try {
            Schema::table('tblproposals', function (Blueprint $table) {
                $table->decimal('discount_percent', 15, 2)->default(0)->change();
                $table->decimal('discount_total', 15, 2)->default(0)->change();
                $table->integer('show_quantity_as')->default(1)->change();
                $table->integer('status')->default(1)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblitemable - amount and tax_rate already added in previous migration

        // tblitems - rate should have default
        try {
            Schema::table('tblitems', function (Blueprint $table) {
                $table->decimal('rate', 15, 2)->default(0)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblnotes - rel_type should be nullable
        try {
            Schema::table('tblnotes', function (Blueprint $table) {
                $table->string('rel_type', 50)->nullable()->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblleads - Make critical columns nullable that might cause issues
        try {
            Schema::table('tblleads', function (Blueprint $table) {
                $table->integer('assigned')->nullable()->default(null)->change();
                $table->integer('addedfrom')->nullable()->default(null)->change();
                $table->integer('lastcontact')->nullable()->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tbltasks - status and priority should have defaults
        try {
            Schema::table('tbltasks', function (Blueprint $table) {
                $table->integer('status')->default(1)->change();
                $table->integer('priority')->default(2)->change();
                $table->integer('addedfrom')->default(0)->change();
                $table->integer('is_public')->default(0)->change();
                $table->integer('billable')->default(1)->change();
                $table->integer('billed')->default(0)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblprojects - status should have default
        try {
            Schema::table('tblprojects', function (Blueprint $table) {
                $table->integer('status')->default(1)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tbltickets - priority, status
        try {
            Schema::table('tbltickets', function (Blueprint $table) {
                $table->integer('priority')->default(2)->change();
                $table->integer('status')->default(1)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblsubscriptions - status
        try {
            Schema::table('tblsubscriptions', function (Blueprint $table) {
                $table->integer('status')->default(1)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblexpenses - category
        try {
            Schema::table('tblexpenses', function (Blueprint $table) {
                $table->integer('category')->default(0)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblcontacts - is_primary, active
        try {
            Schema::table('tblcontacts', function (Blueprint $table) {
                $table->boolean('is_primary')->default(false)->change();
                $table->boolean('active')->default(true)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblclients - currency
        try {
            Schema::table('tblclients', function (Blueprint $table) {
                $table->integer('currency')->default(1)->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }

        // tblstaff - make name fields nullable
        try {
            Schema::table('tblstaff', function (Blueprint $table) {
                $table->string('firstname', 100)->nullable()->change();
                $table->string('lastname', 100)->nullable()->change();
            });
        } catch (\Exception $e) {
            // Error ignored
        }
    }

    public function down(): void
    {
        // Minimal rollback - just skip
    }
};