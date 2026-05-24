<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->safeSchema('tblprojects', function (Blueprint $table) {
            $table->softDeletes();
        });
        $this->addIndexIfNotExists('tblprojects', 'status');
        $this->addIndexIfNotExists('tblprojects', 'clientid');
        $this->addIndexIfNotExists('tblprojects', 'addedfrom');
        $this->safeSchema('tblprojects', function (Blueprint $table) {
            $table->boolean('contact_notification')->default(1)->change();
            $table->json('notify_contacts')->nullable()->change();
        });
        $this->addFKIfNotExists('tblprojects', 'clientid', 'tblclients', 'userid', 'cascade');
        $this->addFKIfNotExists('tblprojects', 'addedfrom', 'tblstaff', 'staffid', 'set null');

        $this->safeSchema('tblproject_members', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblproject_members', 'project_id');
        $this->addIndexIfNotExists('tblproject_members', 'staff_id');
        $this->addFKIfNotExists('tblproject_members', 'project_id', 'tblprojects', 'id', 'cascade');
        $this->addFKIfNotExists('tblproject_members', 'staff_id', 'tblstaff', 'staffid', 'cascade');

        $this->safeSchema('tblproject_activity', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblproject_activity', 'project_id');
        $this->addIndexIfNotExists('tblproject_activity', 'staff_id');
        $this->addIndexIfNotExists('tblproject_activity', 'contact_id');
        $this->addFKIfNotExists('tblproject_activity', 'project_id', 'tblprojects', 'id', 'cascade');
        $this->addFKIfNotExists('tblproject_activity', 'staff_id', 'tblstaff', 'staffid', 'cascade');
        $this->addFKIfNotExists('tblproject_activity', 'contact_id', 'tblcontacts', 'id', 'set null');

        $this->safeSchema('tblproject_files', function (Blueprint $table) {
            $table->softDeletes();
            $table->unsignedBigInteger('file_size')->nullable()->after('filetype');
        });
        $this->addIndexIfNotExists('tblproject_files', 'project_id');
        $this->addIndexIfNotExists('tblproject_files', 'staffid');
        $this->addIndexIfNotExists('tblproject_files', 'contact_id');
        $this->addFKIfNotExists('tblproject_files', 'project_id', 'tblprojects', 'id', 'cascade');
        $this->addFKIfNotExists('tblproject_files', 'staffid', 'tblstaff', 'staffid', 'cascade');
        $this->addFKIfNotExists('tblproject_files', 'contact_id', 'tblcontacts', 'id', 'set null');

        $this->safeSchema('tblproject_notes', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblproject_notes', 'project_id');
        $this->addIndexIfNotExists('tblproject_notes', 'staff_id');
        $this->addFKIfNotExists('tblproject_notes', 'project_id', 'tblprojects', 'id', 'cascade');
        $this->addFKIfNotExists('tblproject_notes', 'staff_id', 'tblstaff', 'staffid', 'cascade');

        $this->safeSchema('tblproject_settings', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblproject_settings', 'project_id');
        $this->addFKIfNotExists('tblproject_settings', 'project_id', 'tblprojects', 'id', 'cascade');

        $this->safeSchema('tblprojectdiscussions', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblprojectdiscussions', 'project_id');
        $this->addIndexIfNotExists('tblprojectdiscussions', 'staff_id');
        $this->addIndexIfNotExists('tblprojectdiscussions', 'contact_id');
        $this->addFKIfNotExists('tblprojectdiscussions', 'project_id', 'tblprojects', 'id', 'cascade');
        $this->addFKIfNotExists('tblprojectdiscussions', 'staff_id', 'tblstaff', 'staffid', 'cascade');
        $this->addFKIfNotExists('tblprojectdiscussions', 'contact_id', 'tblcontacts', 'id', 'set null');

        $this->safeSchema('tblprojectdiscussioncomments', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblprojectdiscussioncomments', 'discussion_id');
        $this->addIndexIfNotExists('tblprojectdiscussioncomments', 'staff_id');
        $this->addIndexIfNotExists('tblprojectdiscussioncomments', 'contact_id');
        $this->addFKIfNotExists('tblprojectdiscussioncomments', 'discussion_id', 'tblprojectdiscussions', 'id', 'cascade');
        $this->addFKIfNotExists('tblprojectdiscussioncomments', 'staff_id', 'tblstaff', 'staffid', 'cascade');
        $this->addFKIfNotExists('tblprojectdiscussioncomments', 'contact_id', 'tblcontacts', 'id', 'set null');
        $this->addFKIfNotExists('tblprojectdiscussioncomments', 'parent', 'tblprojectdiscussioncomments', 'id', 'set null');

        $this->safeSchema('tblpinned_projects', function (Blueprint $table) {
            $table->softDeletes();
            $table->unique(['project_id', 'staff_id']);
        });
        $this->addIndexIfNotExists('tblpinned_projects', 'project_id');
        $this->addIndexIfNotExists('tblpinned_projects', 'staff_id');
        $this->addFKIfNotExists('tblpinned_projects', 'project_id', 'tblprojects', 'id', 'cascade');
        $this->addFKIfNotExists('tblpinned_projects', 'staff_id', 'tblstaff', 'staffid', 'cascade');

        $this->safeSchema('tblmilestones', fn(Blueprint $t) => $t->softDeletes());
        $this->addIndexIfNotExists('tblmilestones', 'project_id');
    }

    private function safeSchema(string $table, callable $callback): void
    {
        try {
            Schema::table($table, $callback);
        } catch (\Exception $e) {
            // Skip if column/index already exists
        }
    }

    private function addIndexIfNotExists(string $table, string $column): void
    {
        $driver = Schema::getConnection()->getDriverName();
        $prefix = Schema::getConnection()->getTablePrefix();

        if ($driver === 'mysql') {
            $indexName = "{$prefix}{$table}_{$column}_index";
            $exists = DB::select("SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?", [$prefix . $table, $indexName]);
            if (!empty($exists)) return;
        }

        try {
            Schema::table($table, fn(Blueprint $t) => $t->index($column));
        } catch (\Exception $e) {
            // Index already exists
        }
    }

    private function addFKIfNotExists(string $table, string $column, string $references, string $referencesColumn, string $onDelete = 'cascade'): void
    {
        $driver = Schema::getConnection()->getDriverName();
        $prefix = Schema::getConnection()->getTablePrefix();

        if ($driver === 'mysql') {
            $fkName = "{$prefix}{$table}_{$column}_foreign";
            $exists = DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'", [$prefix . $table, $fkName]);
            if (!empty($exists)) return;
        }

        try {
            Schema::table($table, function (Blueprint $table) use ($column, $references, $referencesColumn, $onDelete) {
                $table->foreign($column)->references($referencesColumn)->on($references)->onDelete($onDelete);
            });
        } catch (\Exception $e) {
            // FK constraint already exists
        }
    }

    public function down(): void
    {
        $tables = [
            'tblmilestones', 'tblpinned_projects', 'tblprojectdiscussioncomments',
            'tblprojectdiscussions', 'tblproject_settings', 'tblproject_notes',
            'tblproject_files', 'tblproject_activity', 'tblproject_members', 'tblprojects',
        ];

        foreach ($tables as $table) {
            try {
                Schema::table($table, fn(Blueprint $t) => $t->dropSoftDeletes());
            } catch (\Exception $e) {
                //
            }
        }
    }
};
