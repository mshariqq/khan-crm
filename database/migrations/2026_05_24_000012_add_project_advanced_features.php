<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Priority to tblprojects
        Schema::table('tblprojects', function (Blueprint $table) {
            $table->string('priority', 20)->default('medium')->after('progress_from_tasks');
            $table->unsignedBigInteger('category_id')->nullable()->after('priority');
        });

        // Project Categories
        Schema::create('tblproject_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('color', 20)->default('#6b7280');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Project Templates
        Schema::create('tblproject_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->json('milestones')->nullable();
            $table->json('tasks')->nullable();
            $table->json('team_roles')->nullable();
            $table->json('checklists')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Project Phases
        Schema::create('tblproject_phases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name', 191);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('phase_order')->default(0);
            $table->string('color', 20)->default('#3b82f6');
            $table->string('status', 50)->default('pending');
            $table->timestamps();
        });

        // Project Risks
        Schema::create('tblproject_risks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->string('risk_type', 100)->nullable();
            $table->string('probability', 50)->default('medium');
            $table->string('impact', 50)->default('medium');
            $table->string('status', 50)->default('open');
            $table->string('mitigation_plan', 1000)->nullable();
            $table->string('contingency_plan', 1000)->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('identified_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // Project Change Requests
        Schema::create('tblproject_change_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('title', 191);
            $table->text('description');
            $table->string('status', 50)->default('submitted');
            $table->string('priority', 20)->default('medium');
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->integer('estimated_days')->nullable();
            $table->text('impact_analysis')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Project Resource Allocations
        Schema::create('tblproject_resource_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('staff_id');
            $table->string('role', 191)->nullable();
            $table->decimal('allocation_percentage', 5, 2)->default(100);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Task Dependencies
        Schema::create('tbltask_dependencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('depends_on_task_id');
            $table->string('dependency_type', 10)->default('FS');
            $table->integer('lag_days')->default(0);
            $table->timestamps();
        });

        // Project Baselines (EVM)
        Schema::create('tblproject_baselines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name', 191);
            $table->text('description')->nullable();
            $table->decimal('planned_value', 15, 2)->default(0);
            $table->decimal('planned_cost', 15, 2)->default(0);
            $table->integer('planned_duration_days')->default(0);
            $table->date('baseline_date');
            $table->json('scope_snapshot')->nullable();
            $table->json('schedule_snapshot')->nullable();
            $table->json('cost_snapshot')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Project Closure Checklists
        Schema::create('tblproject_closure_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('item', 500);
            $table->string('category', 100)->nullable();
            $table->boolean('completed')->default(false);
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Lessons Learned
        Schema::create('tblproject_lessons_learned', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('title', 191);
            $table->text('description');
            $table->string('category', 100)->nullable();
            $table->string('impact', 50)->default('positive');
            $table->text('recommendation')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Project Shares (public/limited access)
        Schema::create('tblproject_shares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('token', 64)->unique();
            $table->string('email', 191)->nullable();
            $table->string('access_level', 50)->default('view');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('access_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Project Watchers
        Schema::create('tblproject_watchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('staff_id');
            $table->timestamps();
            $table->unique(['project_id', 'staff_id']);
        });

        // Recurring Projects
        Schema::create('tblproject_recurring', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_project_id');
            $table->string('recurring_type', 20)->default('monthly');
            $table->integer('repeat_every')->default(1);
            $table->integer('max_occurrences')->nullable();
            $table->integer('occurrences_generated')->default(0);
            $table->date('next_generation_date');
            $table->date('last_generated_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('copy_team')->default(true);
            $table->boolean('copy_milestones')->default(true);
            $table->boolean('copy_tasks')->default(false);
            $table->timestamps();
        });

        // Project Feedback/Survey
        Schema::create('tblproject_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->integer('rating')->nullable();
            $table->integer('nps_score')->nullable();
            $table->text('comment')->nullable();
            $table->json('survey_responses')->nullable();
            $table->string('source', 50)->default('manual');
            $table->timestamps();
        });

        // Project Locations
        Schema::create('tblproject_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name', 191)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('type', 50)->default('primary');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblproject_locations');
        Schema::dropIfExists('tblproject_feedback');
        Schema::dropIfExists('tblproject_recurring');
        Schema::dropIfExists('tblproject_watchers');
        Schema::dropIfExists('tblproject_shares');
        Schema::dropIfExists('tblproject_lessons_learned');
        Schema::dropIfExists('tblproject_closure_checklists');
        Schema::dropIfExists('tblproject_baselines');
        Schema::dropIfExists('tbltask_dependencies');
        Schema::dropIfExists('tblproject_resource_allocations');
        Schema::dropIfExists('tblproject_change_requests');
        Schema::dropIfExists('tblproject_risks');
        Schema::dropIfExists('tblproject_phases');
        Schema::dropIfExists('tblproject_templates');
        Schema::dropIfExists('tblproject_categories');

        Schema::table('tblprojects', function (Blueprint $table) {
            $table->dropColumn(['priority', 'category_id']);
        });
    }
};
