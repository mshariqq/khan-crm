<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- CORE ENTITIES ---
        Schema::table('tblstaff', function (Blueprint $table) {
            $table->index('firstname');
            $table->index('lastname');
            $table->unique('email');
        });

        Schema::table('tblclients', function (Blueprint $table) {
            $table->index('country');
            $table->index('leadid');
            $table->index('company');
            $table->index('active');
        });

        Schema::table('tblcontacts', function (Blueprint $table) {
            $table->index('userid');
            $table->index('firstname');
            $table->index('lastname');
            $table->index('email');
            $table->index('is_primary');
        });

        Schema::table('tblleads', function (Blueprint $table) {
            $table->index('name');
            $table->index('company');
            $table->index('email');
            $table->index('assigned');
            $table->index('status');
            $table->index('source');
            $table->index('lastcontact');
            $table->index('dateadded');
            $table->index('leadorder');
            $table->index('from_form_id');
        });

        // --- FINANCIALS ---
        Schema::table('tblinvoices', function (Blueprint $table) {
            $table->index('currency');
            $table->index('clientid');
            $table->index('project_id');
            $table->index('sale_agent');
            $table->index('total');
            $table->index('status');
            $table->index('formatted_number');
        });

        Schema::table('tblestimates', function (Blueprint $table) {
            $table->index('clientid');
            $table->index('currency');
            $table->index('project_id');
            $table->index('sale_agent');
            $table->index('status');
            $table->index('formatted_number');
        });

        Schema::table('tblcreditnotes', function (Blueprint $table) {
            $table->index('currency');
            $table->index('clientid');
            $table->index('project_id');
            $table->index('formatted_number');
        });

        Schema::table('tblexpenses', function (Blueprint $table) {
            $table->index('clientid');
            $table->index('project_id');
            $table->index('category');
            $table->index('currency');
        });

        Schema::table('tblinvoicepaymentrecords', function (Blueprint $table) {
            $table->index('invoiceid');
            $table->index('paymentmethod');
        });

        // --- PROJECTS & TASKS ---
        Schema::table('tblprojects', function (Blueprint $table) {
            $table->index('clientid');
            $table->index('name');
        });

        Schema::table('tblproject_members', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('staff_id');
        });

        Schema::table('tbltasks', function (Blueprint $table) {
            $table->index('rel_id');
            $table->index('rel_type');
            $table->index('milestone');
            $table->index('kanban_order');
            $table->index('status');
        });

        Schema::table('tbltaskstimers', function (Blueprint $table) {
            $table->index('task_id');
            $table->index('staff_id');
        });

        Schema::table('tbltask_assigned', function (Blueprint $table) {
            $table->index('taskid');
            $table->index('staffid');
        });

        Schema::table('tbltask_checklist_items', function (Blueprint $table) {
            $table->index('taskid');
        });

        Schema::table('tbltask_comments', function (Blueprint $table) {
            $table->index('file_id');
            $table->index('taskid');
        });

        // --- SUPPORT SYSTEM ---
        Schema::table('tbltickets', function (Blueprint $table) {
            $table->index('service');
            $table->index('department');
            $table->index('status');
            $table->index('userid');
            $table->index('priority');
            $table->index('project_id');
            $table->index('contactid');
        });

        Schema::table('tbldepartments', function (Blueprint $table) {
            $table->index('name');
        });

        // --- UTILITIES & SYSTEM ---
        Schema::table('tblactivity_log', function (Blueprint $table) {
            $table->index('staffid');
        });

        Schema::table('tbloptions', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('tblnotes', function (Blueprint $table) {
            $table->index('rel_id');
            $table->index('rel_type');
        });

        Schema::table('tblfiles', function (Blueprint $table) {
            $table->index('rel_id');
            $table->index('rel_type');
        });

        Schema::table('tbltaggables', function (Blueprint $table) {
            $table->index('rel_id');
            $table->index('rel_type');
            $table->index('tag_id');
        });

        Schema::table('tbltags', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('tblcustomfieldsvalues', function (Blueprint $table) {
            $table->index('relid');
            $table->index('fieldto');
            $table->index('fieldid');
        });

        Schema::table('tblsessions', function (Blueprint $table) {
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        // Dropping indexes in Laravel usually requires naming them. 
        // For a clean slate, we'll leave this empty as we usually drop tables.
    }
};
